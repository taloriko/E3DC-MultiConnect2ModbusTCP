# Einstellungen und Steuerlogik – Abgleich mit BTA V2.10

Diese Seite fasst die für die **E3/DC Multi Connect II** relevanten Einstellungen aus der Betriebsanleitung **„Wallbox multi connect I und II BTA V2.10“** zusammen und stellt sie den bisher durch Reverse Engineering gefundenen Modbus-Funktionen gegenüber.

Wichtig ist die Trennung zwischen:

- Einstellungen, die laut Betriebsanleitung im **übergeordneten E3/DC-Speichersystem** vorgenommen werden,
- Funktionen, die an der Wallbox **direkt per Modbus nachgewiesen** wurden,
- Abläufen, die aus Anleitung und Messverhalten **abgeleitet / vermutet** werden,
- Funktionen, für die bisher **keine direkte Modbus-Zuordnung** gefunden wurde.

Die detaillierten Messwerte und Schreibtests bleiben in [../modbus-register.md](../modbus-register.md). Interne Steckverbinder sind in [../stecker.md](../stecker.md) dokumentiert.

## Statuskennzeichnung

- **Verifiziert** – am eigenen Testgerät reproduzierbar nachgewiesen
- **BTA** – Funktion oder Einstellung steht ausdrücklich in der Betriebsanleitung BTA V2.10
- **Hypothese** – technisch plausibler Ablauf, aber noch nicht durch Mitschnitt bzw. Gegenprobe mit einem E3/DC-Speichersystem bestätigt
- **Offen** – noch keine belastbare Modbus-Zuordnung gefunden

---

## 1. Was laut Betriebsanleitung eingestellt werden kann bzw. muss

| Einstellung / Funktion | BTA V2.10 | Wo erfolgt die Einstellung? | Stand im Reverse Engineering |
|---|---:|---|---|
| I-Max-Drehkodierschalter / Hardware-Maximalstrom | S. 60 | direkt an der Wallbox | **40068** liefert die eingestellte Stromstufe als Rückmeldung; kein Modbus-Schreiben erforderlich |
| Wallbox hinzufügen / IP-Adresse suchen oder eingeben | S. 66 / 97 | E3/DC-Speichersystem | dient der Einbindung in das E3/DC-System |
| Absicherung der Wallbox | S. 67 / 89 | E3/DC-Speichersystem | **Offen:** kein eindeutiges direktes Register zugeordnet |
| DHCP an/aus, IP-Adresse, Subnetzmaske, Gateway | S. 68 / 93 | über E3/DC-Bedienung, Einstellung betrifft die Wallbox | **50013–50018** liefern IP/Gateway/Subnetzmaske lesend; **Coil 10000** ist nur eine DHCP-Hypothese |
| Vorsicherung Hausanschluss / Blackout Prevention | S. 69 | E3/DC-Speichersystem | keine direkte Wallbox-Funktion erwartet; Regelgröße des Energiemanagements |
| Sonnenmodus / Mischbetrieb | S. 75–76, 84, 87 | E3/DC-System; Multi Connect II zusätzlich über Näherungssensor | Frontsensor setzt **Coil 5 = TRUE**; genaue E3/DC-Quittierung ist noch offen |
| Phasenwahl 1-phasig / 3-phasig / auto | S. 76, 88–89 | E3/DC-Speichersystem | **Coil 3** steuert direkt 1P/3P. Die Bedienoption „auto“ wird hier nicht weiter bewertet |
| Max. Ladestrom pro Phase | S. 89 | E3/DC-Speichersystem | **40081** ist als direkter Ladestrom-Sollwert in A verifiziert; ob der gespeicherte GUI-Parameter 1:1 diesem Register entspricht, wurde nicht separat mitgeschnitten |
| Min. Ladestrom pro Phase | S. 89–90 | E3/DC-Speichersystem | kein separates Wallbox-Register gefunden |
| Haltezeit Mindeststrom | S. 90–91 | E3/DC-Speichersystem | kein separates Wallbox-Register gefunden |
| Ladestrategie sofort / verzögert / deaktiviert | S. 91–92 | E3/DC-Speichersystem | kein separates Wallbox-Register gefunden |
| Ziel-Uhrzeit / Mindest-Kapazität je Wochentag | S. 91–92 | E3/DC-Speichersystem | kein separates Wallbox-Register gefunden |
| RFID-Ladeauthentifizierung an/aus | S. 95 | E3/DC-Speichersystem | RFID-Karten-ID ist über **40086–40089** lesbar; Freigabeparameter noch nicht gefunden |
| Laden bei Verbindungsverlust erlaubt / unterbunden | S. 95–96 | E3/DC-Speichersystem | kein separates Freigabebit gefunden; **40082 = 0 A** stoppt bei Verbindungsverlust, >0 A erlaubt im Test das Weiterladen |
| Max. Ladestrom bei Verbindungsverlust | S. 95–96 | E3/DC-Speichersystem | **40082** direkt als Stromgrenze verifiziert |
| Ladepriorität erst Batterie / erst Wallbox | S. 98 | E3/DC-Speichersystem | kein separates Wallbox-Register gefunden |
| Batterieentladung durch Wallbox im Sonnenmodus | S. 98–99 | E3/DC-Speichersystem | kein separates Wallbox-Register gefunden |
| Batterieentladung bis SOC | S. 98–99 | E3/DC-Speichersystem | kein separates Wallbox-Register gefunden |
| Batterieentladung im Mischbetrieb / Haltezeit / Zeitfunktion | S. 99 | E3/DC-Speichersystem | kein separates Wallbox-Register gefunden |
| Wallbox im Notstrom erlauben | S. 100–101 | E3/DC-Speichersystem | kein separates Wallbox-Register gefunden |
| Startwert Ladeleistung pro Phase im Notstrom | S. 101 | E3/DC-Speichersystem | kein separates Wallbox-Register gefunden |
| Max. Ladeleistung pro Phase im Notstrom | S. 101 | E3/DC-Speichersystem | kein separates Wallbox-Register gefunden |
| Reservierte Batteriekapazität für Hausverbrauch | S. 101 | E3/DC-Speichersystem | keine direkte Wallbox-Funktion erwartet |

Die BTA sagt in Kapitel **8.7**, dass die dort gezeigten Einstellungen in den Bedienmenüs der **übergeordneten Speichersysteme von E3/DC** vorgenommen werden. Deshalb ist nicht zu erwarten, dass jeder sichtbare Menüparameter zwingend als eigenes Register in der Wallbox existiert.

---

## 2. Direkt an der Wallbox gefundene Funktionen

| Modbus | Bedeutung | Stand |
|---|---|---|
| **Coil 1** | Steckerverriegelung / Verriegelungsfreigabe | **Verifiziert:** bei 0 wird der Stecker nicht mehr verriegelt |
| **Coil 3** | Phasenwahl | **Verifiziert:** 0 = 3-phasig, 1 = 1-phasig; Übernahme beim nächsten Ladebeginn |
| **Coil 5** | Frontsensor / Lademodus-Umschaltanforderung | **Verifiziertes Verhalten:** Sensor 1–4 s → TRUE; BTA ordnet den Sensor Sonnenmodus/Mischbetrieb zu |
| **Coil 6** | Stecker nach Ladeende verriegelt lassen | **Verifiziert** |
| **40068** | I-Max-Drehkodierschalter | **Verifiziert lesend** |
| **40069** | erkannter PP-Kabelnennstrom | **Verifiziert** |
| **40070** | interner EVSE-/Ladezustandscode | **Verifiziert als interner Zustandscode** |
| **40071** | CP-PWM Duty Cycle | **Verifiziert** |
| **40072–40074** | Strom L1/L2/L3, 0,1 A/Digit | **Verifiziert** |
| **40081** | Ladestrom-Sollwert / angebotener Maximalstrom in A | **RW verifiziert** |
| **40082** | Max. Ladestrom bei Verbindungsverlust | **RW verifiziert** |
| **40086–40089** | RFID-Karten-ID | **Verifiziert lesend** |
| **50013–50014** | IPv4-Adresse | **Verifiziert lesend** |
| **50015–50016** | Gateway | **Verifiziert lesend** |
| **50017–50018** | Subnetzmaske | **Verifiziert lesend** |

Die vollständigen Registerdetails, Skalierungen und Testabläufe stehen in [../modbus-register.md](../modbus-register.md).

### 40082 und die zwei Einstellungen aus der BTA

Die Bedienoberfläche der BTA zeigt getrennt:

1. **Laden bei Verbindungsverlust** – erlaubt / unterbunden
2. **Max. Ladestrom bei Verbindungsverlust**

Im direkten Modbus-Test wurde bisher nur **40082** als passender Stellwert identifiziert:

- **40082 = 0 A** → Laden wird nach Verbindungsverlust beendet
- **40082 > 0 A** → Weiterladen mit der vorgegebenen Stromgrenze ist möglich

Daraus ergibt sich die Arbeitshypothese, dass beide GUI-Einstellungen intern möglicherweise in einem Wert zusammengefasst werden: **0 = unterbunden**, **>0 = erlaubt und gleichzeitig Stromgrenze**. Ein separates Freigabebit wurde bisher nicht gefunden.

---

## 3. Näherungssensor: bestätigtes Verhalten und vermuteter Ablauf

Die BTA V2.10 beschreibt für die Multi Connect II:

- Näherungssensor **1 bis 4 Sekunden** berühren
- damit zwischen **Sonnenmodus und Mischbetrieb** umschalten
- die Umschaltung zwischen diesen Lademodi ist nur in Verbindung mit einem **E3/DC-Speichersystem** möglich

Eigener Modbus-Test:

- Frontsensor 1–4 s betätigen → **Coil 5 = TRUE**
- Frontsensor erneut betätigen → Coil 5 bleibt **TRUE**
- Coil 5 per Modbus auf **FALSE** setzen
- Frontsensor erneut betätigen → Coil 5 wird wieder **TRUE**

Damit passt folgender Ablauf am besten zu den bisherigen Beobachtungen:

```text
Näherungssensor 1–4 s
        ↓
Coil 5 wird TRUE
        ↓
E3/DC-Speichersystem erkennt die Umschaltanforderung   [Hypothese]
        ↓
E3/DC-System verarbeitet den Wechsel Sonnenmodus ↔ Mischbetrieb
        ↓
Coil 5 wird quittiert / auf FALSE zurückgesetzt         [Hypothese]
```

Sicher nachgewiesen ist nur das Verhalten **Sensor → Coil 5** und die erneute Auslösbarkeit nach einem Rücksetzen auf FALSE. Die tatsächliche Quittierung durch ein E3/DC-Speichersystem wurde noch nicht mitgeschnitten.

---

## 6. Funktionen, die sehr wahrscheinlich im E3/DC-Energiemanagement liegen

Die folgenden Funktionen benötigen Informationen bzw. Zustände des Speichersystems, die der Wallbox selbst nicht vollständig vorliegen. Zusätzlich werden sie in der BTA als Einstellungen des übergeordneten E3/DC-Systems beschrieben.

Daher wird derzeit **nicht davon ausgegangen, dass dafür zwingend jeweils ein eigenes Wallbox-Register existieren muss**:

- Vorsicherung Hausanschluss / Blackout Prevention
- Min. Ladestrom für den Sonnenmodus
- Haltezeit Mindeststrom
- Ladestrategie sofort / verzögert / deaktiviert
- Ziel-Uhrzeiten und Mindest-kWh je Wochentag
- Ladepriorität Batterie / Wallbox
- Batterieentladung durch die Wallbox
- SOC-Grenze der Batterieentladung
- Batterie-/Netzbezug während Haltezeit und Zeitfunktion
- Notstrom-Freigabe und Notstrom-Leistungsstrategie
- reservierte Batteriekapazität für den Hausverbrauch

**Wichtig:** Das ist eine Einordnung des aktuellen Reverse-Engineering-Stands und kein Beweis, dass niemals interne Parameter dazu in der Wallbox vorhanden sind.

---

## 7. Noch nicht gefunden bzw. weiter offen

| Funktion / Bereich | Aktueller Stand |
|---|---|
| **Coil 2** | RW, aber Bedeutung weiterhin unbekannt |
| **Coil 4** | RW-Test; fällt ungefähr alle 60 s auf FALSE zurück, Bedeutung unbekannt |
| **Coil 7** | RO; Bedeutung unbekannt |
| **40084** | RO; Bedeutung unbekannt |
| **40102** | RO; bleibt auch beim Wechsel 1P/3P auf Wert 3, frühere Phasen-Hypothese widerlegt |
| **RFID-Ladeauthentifizierung an/aus** | keine direkte Modbus-Zuordnung gefunden |
| **DHCP an/aus** | Coil 10000 zeigt Netzwerkverhalten, DHCP-Zuordnung bleibt Hypothese |
| **statische IP / Gateway / Subnetzmaske schreiben** | Lesewerte gefunden; direkter Schreibmechanismus noch nicht zugeordnet |
| **Absicherung der Wallbox** | kein eindeutiges Register gefunden |
| **separate Freigabe „Laden bei Verbindungsverlust“** | kein separates Bit gefunden; mögliche Abbildung über 40082 = 0 / >0 |
| **direkte selbst verifizierte Zuordnung des GUI-Schalters Sonnenmodus an/aus** | weiterhin offen |
| **55000–55126** | Block größtenteils funktional unbekannt; Details siehe Registerdokumentation |
| **Coil-5-Quittierung durch E3/DC** | Sensorfunktion ist bekannt, Rücksetz-/Quittierablauf des übergeordneten Systems noch nicht mitgeschnitten |

---

## 8. Interne Anschlüsse und Bedienelemente

### J13 – Sensoranschluss 6 mA

In der BTA V2.10 ist in der Innenansicht, **Abb. 4**, die Position **[1]** ausdrücklich als **„Sensoranschluss 6 mA“** bezeichnet.

Auf der untersuchten Hauptplatine ist der entsprechende 6-polige Anschluss mit **J13** beschriftet.

Damit wird J13 als **Sensoranschluss 6 mA** dokumentiert. Die genaue Pinbelegung wurde bisher nicht elektrisch nachverfolgt.

### J16 – 4-poliger Anschluss

J16 ist auf der untersuchten Platine nicht belegt. In der BTA V2.10 konnte keine Funktion gefunden werden, die sich diesem Anschluss belastbar zuordnen lässt.

Die bisherigen elektrischen Messungen sind unter [../stecker.md](../stecker.md) dokumentiert. Weitere Versuche wurden bewusst beendet, um eine Beschädigung der Wallbox zu vermeiden.

### BP1 – Taster auf Haupt- und Netzwerkplatine

Die BTA V2.10 beschreibt die beiden Taster nicht unter der Platinenbezeichnung **BP1**.

Eigene Tests zeigen:

- **BP1 Netzwerkplatine:** etwa 10 s drücken reaktiviert die Netzwerkverbindung nach dem Test mit Coil 10000; Funktion als Netzwerk-Rücksetzung / Reaktivierung ist damit nachgewiesen
- **BP1 Hauptplatine:** bisher keine eindeutige Funktion gefunden

Die BP1-Zuordnungen stammen daher aus eigenen Tests und nicht aus der Betriebsanleitung.

---

## Kurzfazit

Für eine direkte Steuerung ohne E3/DC-Hauskraftwerk sind aktuell vor allem die tatsächlich gefundenen Wallbox-Stellwerte relevant:

- **40081** – Ladestrom-Sollwert
- **40082** – Stromgrenze bei Kommunikationsverlust
- **Coil 3** – 1P/3P-Phasenwahl
- **Coil 1 / 6** – Steckerverriegelung
- **Coil 5** – Frontsensor-Umschaltanforderung

Die umfangreicheren Funktionen wie PV-Regelstrategie, Haltezeiten, Batteriepriorisierung und Notstromlogik sind nach aktuellem Stand Aufgaben des **E3/DC-Energiemanagements** und müssen für einen vollständigen Standalone-Betrieb gegebenenfalls im externen Controller nachgebildet werden.
