#import "@preview/cetz:0.4.2"

// Funzione per determinare il colore in base al punteggio (Stile Lighthouse)
#let get-score-color(val) = {
  let n = int(val)
  if n >= 90 { rgb("#0cce6b") } // Verde
  else if n >= 50 { rgb("#ffa400") } // Giallo/Arancio
  else { rgb("#ff4e42") } // Rosso
}

#let lighthouse_gauge(label, value) = {
  let color = get-score-color(value)
  let val-int = int(value)
  
  // Calcolo dell'angolo: 100% = 360 gradi
  // Partiamo da 90deg (in alto) e andiamo in senso orario (negativo)
  let end-angle = -360deg * (val-int / 100)

  box(width: 80pt)[
    #align(center)[
      #cetz.canvas({
        import cetz.draw: *
        
        // 1. Cerchio di sfondo (grigio chiaro)
        // arc((0,0), start: 0deg, stop: 360deg, radius: 1.5, stroke: (thickness: 8pt, paint: luma(230)))
        
        // 2. Arco del punteggio (colorato)
        // Nota: cap: "round" rende le estremità arrotondate
          arc((0,0),anchor: "origin", start: 90deg, delta: end-angle, radius: 1.5, stroke: (thickness: 8pt, paint: color, cap: "round"))
        content((0,0), text(size: 2em, weight: "bold", fill: color, font: "Roboto")[#calc.round(value,digits: 2)])
      })
      
      #v(10pt)
      // 4. Etichetta sotto il cerchio
      #text(size: 1.6em, weight: "bold")[#label]
    ]
  ]
}



// Funzione per creare una "Card" di valutazione
#let score_card(title,valoreD,valoreM) = {
  block(
    width: 100%,
    inset: 20pt,
    radius: 12pt,
    fill: luma(90%),
    stroke: luma(20%),
    outset: 0pt,
    spacing: 0pt,
    [
      #strong(text(size: 3em,title)) \ \
      #strong(text(size: 2em,"Desktop:"))
      #columns(4)[
        #lighthouse_gauge("Performance",valoreD.Performance)
        #colbreak()
        #lighthouse_gauge("Accessibility",valoreD.Accessibility)        
        #colbreak()
        #lighthouse_gauge("Best Practices",valoreD.at("Best_Practices"))        
        #colbreak()
        #lighthouse_gauge("SEO",valoreD.SEO)        
      ]
      #strong(text(size: 2em,"Mobile:"))
      #columns(4)[
        #lighthouse_gauge("Performance",valoreM.Performance)
        #colbreak()
        #lighthouse_gauge("Accessibility",valoreM.Accessibility)        
        #colbreak()
        #lighthouse_gauge("Best Practices",valoreM.at("Best_Practices"))        
        #colbreak()
        #lighthouse_gauge("SEO",valoreM.SEO)        
      ]

    ]
      
      
    
  )
}




#let risultati_test_desktop=csv(
  "lighthouse_results/lighthouse_desktopT.csv"
  ).map(
    items=>{
      (
        pagina:items.at(0),
        Performance:items.at(1),
        Accessibility:items.at(2),
        Best_Practices:items.at(3),
        SEO:items.at(4)
        )
    }
  )
#let risultati_test_mobile=csv(
  "lighthouse_results/lighthouse_mobileT.csv"
  ).map(
    items=>{
      (
        pagina:items.at(0),
        Performance:items.at(1),
        Accessibility:items.at(2),
        Best_Practices:items.at(3),
        SEO:items.at(4)
        )
    }
  )
  #let sink=risultati_test_desktop.remove(0)

  #let sink=risultati_test_mobile.remove(0)

#let tot_pagine=risultati_test_mobile.len()

#let tot_desktop=(
        Performance:
          risultati_test_desktop.map(
            it=>{
              int(it.Performance)
            }
          ).sum()/tot_pagine,
        Accessibility:risultati_test_desktop.map(
            it=>{
              int(it.Accessibility)
            }
          ).sum()/tot_pagine,
        Best_Practices:risultati_test_desktop.map(
            it=>{
              int(it.Best_Practices)
            }
          ).sum()/tot_pagine,
        SEO:risultati_test_desktop.map(
            it=>{
              int(it.SEO)
            }
          ).sum()/tot_pagine,
)
#let tot_mobile=(
        Performance:
          risultati_test_mobile.map(
            it=>{
              int(it.Performance)
            }
          ).sum()/tot_pagine,
        Accessibility:risultati_test_mobile.map(
            it=>{
              int(it.Accessibility)
            }
          ).sum()/tot_pagine,
        Best_Practices:risultati_test_mobile.map(
            it=>{
              int(it.Best_Practices)
            }
          ).sum()/tot_pagine,
        SEO:risultati_test_mobile.map(
            it=>{
              int(it.SEO)
            }
          ).sum()/tot_pagine,
)

= Appendice
== Risultati lighthouse <test_lh>
#score_card("",tot_desktop,tot_mobile)

// #let row=1
// #while  row< risultati_test_desktop.len()-1{

// score_card(risultati_test_desktop.at(row).pagina,risultati_test_desktop.at(row),risultati_test_mobile.at(row))
// pagebreak()
// row=row+1
// }