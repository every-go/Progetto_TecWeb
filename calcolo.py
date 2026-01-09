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

# Controllo contrasto fra 3 colori
def check_triplet(c1, c2, c3, min_ratio=4.5):
    r12 = contrast_ratio(c1, c2)
    r13 = contrast_ratio(c1, c3)
    r23 = contrast_ratio(c2, c3)
    ok = (r12 >= min_ratio) and (r13 >= min_ratio) and (r23 >= min_ratio)
    return ok, round(r12,2), round(r13,2), round(r23,2)

ok, r12, r13, r23 = check_triplet("#FAF3DD", "#0048f8", "#000000")
print(ok, r12, r13, r23)
