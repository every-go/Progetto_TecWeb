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

# Dati due colori, trova un terzo con contrasto minimo richiesto
def find_third_color(c1, c2, min_ratio=4.5, step=8):
    best = None
    for r in range(0, 256, step):
        for g in range(0, 256, step):
            for b in range(0, 256, step):
                c3 = f"#{r:02X}{g:02X}{b:02X}"
                if contrast_ratio(c1, c3) >= min_ratio and contrast_ratio(c2, c3) >= min_ratio:
                    return c3
    return None

# Esempio:
result = find_third_color("#000000", "#FAF3DD")
print(result)
