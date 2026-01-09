# Funzioni WCAG
def hex_to_rgb_normalized(hex_color):
    hex_color = hex_color.lstrip("#")
    r = int(hex_color[0:2], 16) / 255
    g = int(hex_color[2:4], 16) / 255
    b = int(hex_color[4:6], 16) / 255
    return r, g, b

def luminance(r, g, b):
    def channel(c):
        return c/12.92 if c <= 0.03928 else ((c+0.055)/1.055)**2.4
    return 0.2126*channel(r) + 0.7152*channel(g) + 0.0722*channel(b)

def contrast_ratio(c1, c2):
    L1 = luminance(*hex_to_rgb_normalized(c1))
    L2 = luminance(*hex_to_rgb_normalized(c2))
    Lmax, Lmin = max(L1, L2), min(L1, L2)
    return (Lmax + 0.05) / (Lmin + 0.05)

# ---------------------------------------------------------------------------- #
#                  step 1 permette la ricerca di 256^3 colori,                 #
# ---------------- step=8 esplori solo i valori 0, 8, 16, ... ---------------- #
# ---------------------------------------------------------------------------- #

def find_third_color(c1, c2, step=1):
    """Trova il colore #RRGGBB che massimizza min(contrast(c1,c3), contrast(c2,c3)).
    Scansione in passi 'step' (default 8). Restituisce None se non trova nulla (improbabile)."""

    # Precalcola le luminanze sorgenti per evitare ricalcoli ripetuti
    L1 = luminance(*hex_to_rgb_normalized(c1))
    L2 = luminance(*hex_to_rgb_normalized(c2))

    def contrast_from_lums(La, Lb):
        Lmax, Lmin = max(La, Lb), min(La, Lb)
        return (Lmax + 0.05) / (Lmin + 0.05)

    best_color = None
    best_score = -1.0  # punteggio = min(contrast1, contrast2)
    best_secondary = -1.0  # tie-break: somma dei due contrasti

    for r in range(0, 256, step):
        for g in range(0, 256, step):
            for b in range(0, 256, step):
                # calcolo luminanza di c3 direttamente in spazio 0..1
                rr, gg, bb = r/255, g/255, b/255
                L3 = luminance(rr, gg, bb)

                c1c3 = contrast_from_lums(L1, L3)
                c2c3 = contrast_from_lums(L2, L3)
                score = min(c1c3, c2c3)
                secondary = c1c3 + c2c3

                if score > best_score or (score == best_score and secondary > best_secondary):
                    best_score = score
                    best_secondary = secondary
                    best_color = f"#{r:02X}{g:02X}{b:02X}"

                    # massimo teorico approssimato (21:1) — se raggiunto, esci subito
                    if best_score >= 21.0:
                        return best_color

    return best_color

# Esempio:
result = find_third_color("#000000", "#555555")
print(result)
