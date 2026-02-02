import csv
import pandas as pd

def trasponi_csv_std():
    with open('Relazione/content/lighthouse_results/lighthouse_desktop.csv', 'r') as f:
        reader = csv.reader(f)
        righe = list(reader)

    # Trasposizione usando la funzione zip
    trasposta = list(zip(*righe))

    with open('Relazione/content/lighthouse_results/lighthouse_desktopT.csv', 'w', newline='') as f:
        writer = csv.writer(f)
        writer.writerows(trasposta)



def trasponi_csv_pandas(file_input, file_output):
    try:
        # Carica il file CSV
        # Nota: se la prima colonna deve diventare la riga delle intestazioni, 
        # aggiungi index_col=0. Se non hai intestazioni, aggiungi header=None.
        df = pd.read_csv(file_input)

        # Esegue la trasposizione
        df_trasposta = df.transpose()

        # Salva il risultato in un nuovo file CSV
        # index=True serve per mantenere i nomi delle righe originali (che ora sono colonne)
        df_trasposta.to_csv(file_output, header=False)
        
        print(f"Trasposizione completata con successo! File salvato come: {file_output}")
    
    except Exception as e:
        print(f"Si è verificato un errore: {e}")

# Utilizzo dello script
file_da_leggere = "Relazione/content/lighthouse_results/lighthouse_mobile.csv"
file_di_destinazione = 'Relazione/content/lighthouse_results/lighthouse_mobileT.csv'

trasponi_csv_pandas(file_da_leggere, file_di_destinazione)
