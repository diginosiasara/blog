#!/usr/bin/env python3
"""BLOG_reorganizado.docx -> data/blog.json + content/<slug>.html
Elimina duplicados (versiones n de m), fichas sin texto y restos de LinkedIn."""
import json, re, subprocess, sys, unicodedata
SRC = sys.argv[1]
MES = {m: i + 1 for i, m in enumerate("enero febrero marzo abril mayo junio julio agosto septiembre octubre noviembre diciembre".split())}
TOPICS = ["Formación profesional y docencia", "Salud y organización sanitaria", "Documentación sanitaria e historia clínica", "Sistemas de información sanitaria", "Codificación clínica y CIE-10-ES", "Arquitectura de software", "IA local, desarrollo con IA y aprendizaje", "Embeddings y procesamiento del lenguaje", "Teleasistencia", "Trayectoria profesional"]
ast = json.loads(subprocess.check_output(["pandoc", SRC, "-t", "json"]))

def txt(x):
    if isinstance(x, list): return "".join(txt(i) for i in x)
    if isinstance(x, dict):
        t, c = x.get("t"), x.get("c")
        if t == "Str": return c
        if t in ("Space", "SoftBreak"): return " "
        if t == "LineBreak": return "\n"
        if t in ("Para", "Plain"): return txt(c) + "\n"
        return txt(c) if c is not None else ""
    return ""

def render(bl):
    doc = {"pandoc-api-version": ast["pandoc-api-version"], "meta": {}, "blocks": bl}
    h = subprocess.run(["pandoc", "-f", "json", "-t", "html", "--wrap=none"], input=json.dumps(doc), capture_output=True, text=True).stdout
    h = re.sub(r"<(p|figure|div)>\s*</\1>", "", re.sub(r"<img[^>]*>", "", h))
    return re.sub(r"<(/?)h([1-5])", lambda m: f"<{m[1]}h{int(m[2]) + 1}", h)   # la página ya tiene su <h1>

def slug(s):
    s = unicodedata.normalize("NFKD", s).encode("ascii", "ignore").decode().lower()
    return re.sub(r"[^a-z0-9]+", "-", s).strip("-")[:70]

cards, details, cur = [], [], None
for b in ast["blocks"]:
    if b["t"] == "Table":
        full = txt(b).replace("\xa0", " ").strip()
        if full.startswith("ENTRADA") and "TAGS" in full: cards.append(full); continue
        if full.startswith("ENTRADA") and "Sara Lopez-Figueredo" in full:
            cur = {"head": full, "blocks": []}; details.append(cur); continue
    if cur is not None: cur["blocks"].append(b)
assert len(cards) == len(details), (len(cards), len(details))

# tema de cada entrada: las cabeceras de tema aparecen en orden; se asigna por la línea 'Sara ... · Tema'
entries, seen, dropped = [], set(), {"duplicadas": 0, "sin_texto": 0}
for card, d in zip(cards, details):
    L = [l.strip() for l in d["head"].split("\n") if l.strip()]
    m = re.match(r"ENTRADA\s+\d+\s+·\s+(ARTÍCULO|PUBLICACIÓN)(?:\s+·\s+(\d+) DE (\w+) DE (\d{4}))?", L[0])
    kind = "articulo" if m.group(1) == "ARTÍCULO" else "publicacion"
    date = f"{m.group(4)}-{MES[m.group(3).lower()]:02d}-{int(m.group(2)):02d}" if m.group(2) else None
    clean = re.sub(r"\s*\(publicación\)", "", L[1]).strip()
    topic = next((t for t in TOPICS if L[-1].endswith(t)), "")
    key = slug(clean) + kind
    if key in seen: dropped["duplicadas"] += 1; continue
    body, vistos = [], set()
    for b in d["blocks"]:
        t = txt(b).strip()
        if t == "Comentarios": break            # comentarios de LinkedIn: fuera
        if b["t"] == "Para" and len(t) > 80:
            if t in vistos: break               # contenido pegado dos veces: se corta
            vistos.add(t)
        only_hash = t.startswith("#") and len(re.findall(r"#\w", t)) >= 2 and not re.search(r"[.!?]\s", t)
        if re.match(r"^(↑ Volver al índice|Comentarios|-{3,})$", t) or only_hash: continue
        body.append(b)
    html = render(body)
    html = re.sub(r"(En mi publicación anterior.*?)desarrollan(En mi publicación anterior)", r"\2", html, flags=re.S)
    if len(re.sub(r"<[^>]+>", " ", html).split()) < 25: dropped["sin_texto"] += 1; continue
    seen.add(key)
    CL = [l.strip() for l in card.split("\n") if l.strip()]
    c = " ".join(CL)
    tags = [x.strip() for x in re.split(r"\s{2,}", re.search(r"TAGS\s+(.*?)\s+HASHTAGS", c).group(1)) if x.strip()]
    summ = CL[2] if len(CL) > 3 and not CL[2].startswith("TAGS") else ""
    e = {"slug": slug(clean), "title": clean, "type": kind, "date": date, "topic": topic, "summary": summ[:220],
         "tags": tags[:3], "hashtags": re.findall(r"#\w+", c.split("HASHTAGS")[1])[:8], "minutes": max(1, round(len(re.sub(r"<[^>]+>", " ", html).split()) / 200))}
    entries.append(e)
    open(f"content/{e['slug']}.html", "w", encoding="utf-8").write(html)
json.dump({"topics": [{"slug": slug(t), "name": t} for t in TOPICS], "entries": entries}, open("data/blog.json", "w", encoding="utf-8"), ensure_ascii=False, indent=1)
print(len(cards), "originales ->", len(entries), "entradas", dropped)
