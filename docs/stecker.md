# Interne Steckverbinder und Anschlussbelegung

Diese Seite dokumentiert ausschließlich die bei der **E3/DC Multi Connect II** vorgefundenen internen Steckverbinder, deren geprüfte Verdrahtung und noch offene Zuordnungen.

Die Angaben beziehen sich auf das im Projekt untersuchte Gerät. Verifizierte Verdrahtungen werden von Hypothesen ausdrücklich getrennt.

---

## Übersicht

| Stecker | Pins / Kontakte | Zuordnung | Status |
|---|---:|---|---|
| **J8** | 5-polig | Ansteuerung 2-poliger Schütz Benedikt R40-20 230 für L2/L3 | **Verdrahtung geprüft** |
| **J9** | 6 | Stromwandler L1 / L2 / L3 | **Verdrahtung geprüft** |
| **J12** | 5 genutzt | PP, CP, PE und Motor Steckerverriegelung | **Verdrahtung geprüft** |
| **J14** | 2-polig | Spannungsrückmeldung L1 hinter dem Ausgangsschütz; sehr wahrscheinlich Schützklebeüberwachung | **Verdrahtung geprüft; Funktion durch Fehler-Simulation stark gestützt** |
| **J15** | 2-polig | N / L vom 2-poligen C16-Automaten Hager NFT716 | **Verdrahtung geprüft** |
| **J17** | 3-polig | Spulenanschluss des 3-poligen Ausgangsschützes | **Verdrahtung geprüft, Pin 3 offen** |
| **J5** | 3-polig | nicht belegt, Funktion unbekannt | offen |
| **J10** | 6-polig | Funktion unbekannt | offen |
| **J13** | 6-polig | laut mitgelieferter Anleitung Sensoranschluss 6 mA | Dokumentationshinweis |
| **J16** | 4-polig | Funktion unbekannt | offen |
| **J19** | 4-polig | Summenstromwandler; L1, L2, L3 und N werden gemeinsam durchgeführt | **Hardware beobachtet; 6-mA-DC-Überwachung vermutet** |
| **J1** | USB-A | Funktion unbekannt | offen |
| **J18** | 2-polig | S0+ / S0- | Beschriftung geprüft |

---


## J8 – 2-poliger Schütz L2/L3

Die Verkabelung wurde geprüft.

Der zugehörige Schütz ist ein **Benedikt R40-20 230**. Über diesen 2-poligen Schütz werden **L2 und L3** geführt.

| Kontakt | Beschriftung | Leitung / Zuordnung |
|---:|---|---|
| **1** | D/N | frei |
| **2** | D/N | frei |
| **3** | NST | Blau → **A1** des 2-poligen Schützes |
| **4** | keine Beschriftung | frei |
| **5** | LST | Gelb → **A2** des 2-poligen Schützes |

---

## J9 – Stromwandler

Die Verkabelung wurde geprüft.

Alle sechs Leitungen am Stecker sind **grau**.

| Kontakt | Zuordnung |
|---:|---|
| **10 / 11** | Stromwandler **L1** |
| **12 / 13** | Stromwandler **L2** |
| **14 / 15** | Stromwandler **L3** |

Die drei Stromwandler passen zu den im Modbus gefundenen Phasenstromwerten 40072–40074.

---

## J12 – Fahrzeuganschluss / Steckerverriegelung

Die Verkabelung wurde geprüft.

| Kontakt | Funktion | Leitungsfarbe |
|---:|---|---|
| **20** | PP – Proximity Pilot | Lila |
| **21** | CP – Control Pilot | Orange |
| **22** | PE – Schutzleiter | Grün/Gelb |
| **23** | Motor Steckerverriegelung **+** | Rot |
| **24** | Motor Steckerverriegelung **-** | Schwarz |

---


## J14 – Spannungsrückmeldung L1 / Schützklebeüberwachung

Die Verkabelung wurde geprüft.

| Kontakt | Beschriftung | Leitung / Zuordnung |
|---:|---|---|
| **1** | WS | Braun → **L1**, Abgriff zwischen Typ-2-Stecker und 3-poligem Ausgangsschütz **Benedikt R40-40 230** |
| **2** | keine Beschriftung | frei |

### Test Schützklebeüberwachung

Zur Simulation eines klebenden Ausgangsschützes wurde bei laufendem Mode-3-Lasttest **Eingang und Ausgang des 3-poligen Schützes überbrückt**. Anschließend wurde die Ladung über die CP-Zustände bis zum Ladeende zurückgenommen.

Beobachtung nach dem Abschalten des Schützes:

- am L1-Abgriff von J14 lag wegen der Brücke weiterhin Spannung an
- die Front-LED der Wallbox leuchtete **rot**
- **Register 40085 = 130**
- **Coil 8 = TRUE**
- **Register 40070 / Zustandscode = `F\0`**

Damit ist sehr stark gestützt, dass J14 die **Spannungsrückmeldung für die Schützklebeüberwachung** bereitstellt: Die Steuerung schaltet den Schütz ab, erkennt hinter dem Schütz aber weiterhin Netzspannung.

Die Betriebsanleitung nennt eine integrierte **Schützklebeüberwachung**, ordnet ihr jedoch keine interne Steckverbindung oder Modbus-Adresse zu. Die Zuordnung zu J14 ergibt sich daher aus diesem eigenen Funktionstest.

---

## J15 – Versorgung vom Leitungsschutzschalter

Die Verkabelung wurde geprüft.

J15 ist mit dem **2-poligen C16-Automaten Hager NFT716** verbunden.

| Kontakt | Leitung / Zuordnung |
|---:|---|
| **1** | **N**, Blau |
| **2** | **L**, Braun, vom 2-poligen C16-Automaten |

---

## J17 – Spule 3-poliger Ausgangsschütz

Die Verkabelung wurde geprüft.

| Kontakt | Beschriftung | Leitung / Zuordnung |
|---:|---|---|
| **1** | KM3 | Gelb → **A2** des 3-poligen Schützes |
| **2** | N | Blau → **A1** des 3-poligen Schützes |
| **3** | — | noch nicht zugeordnet |

---

## J5

- **3-polig**
- nicht belegt
- Funktion bisher unbekannt

---

## J10

- **6-polig**
- Funktion bisher unbekannt

---

## J13

- **6-polig**
- laut der mitgelieferten Anleitung als **Sensoranschluss 6 mA** bezeichnet

Die genaue interne Zuordnung wurde bisher nicht elektrisch nachverfolgt.

---

## J16

- **4-polig**
- nicht belegt
- Funktion bisher unbekannt

---

## J19 – Summenstromwandler

J19 ist **4-polig** und führt zum Summenstromwandler.

Durch den Wandler werden gemeinsam geführt:

- L1
- L2
- L3
- N

Aufgrund des Aufbaus liegt die Vermutung nahe, dass dieser Wandler zur **6-mA-DC-Fehlerstromüberwachung** gehört.

Diese Funktionszuordnung ist aktuell eine **Hypothese** und noch nicht elektrisch bzw. über einen gezielten Fehlerstromtest bestätigt.

---

## J1 – USB-A

- Steckertyp: **USB-A**
- Funktion bisher unbekannt

---

## J18 – S0

J18 ist **2-polig** beschriftet mit:

| Kontakt | Funktion |
|---|---|
| **S0+** | S0 Plus |
| **S0-** | S0 Minus |

Die weitere interne bzw. externe Nutzung wurde bisher nicht untersucht.

---

## Offene Punkte

- J17 Pin 3 zuordnen
- Funktion von J5 klären
- Funktion von J10 klären
- Funktion von J16 klären
- Funktion von J1 klären
- Zusammenhang zwischen J13 und J19 bei der 6-mA-DC-Überwachung klären
- Nutzung von J18 / S0 prüfen
