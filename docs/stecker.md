# Interne Steckverbinder und Anschlussbelegung

Diese Seite dokumentiert ausschließlich die bei der **E3/DC Multi Connect II** vorgefundenen internen Steckverbinder, deren geprüfte Verdrahtung und noch offene Zuordnungen.

Die Angaben beziehen sich auf das im Projekt untersuchte Gerät. Verifizierte Verdrahtungen werden von Hypothesen ausdrücklich getrennt.

---

## Übersicht

| Stecker | Pins / Kontakte | Zuordnung | Status |
|---|---:|---|---|
| **J9** | 6 | Stromwandler L1 / L2 / L3 | **Verdrahtung geprüft** |
| **J12** | 5 genutzt | PP, CP, PE und Motor Steckerverriegelung | **Verdrahtung geprüft** |
| **J5** | 3-polig | nicht belegt, Funktion unbekannt | offen |
| **J10** | 6-polig | Funktion unbekannt | offen |
| **J13** | 6-polig | laut mitgelieferter Anleitung Sensoranschluss 6 mA | Dokumentationshinweis |
| **J16** | 4-polig | Funktion unbekannt | offen |
| **J19** | 4-polig | Summenstromwandler; L1, L2, L3 und N werden gemeinsam durchgeführt | **Hardware beobachtet; 6-mA-DC-Überwachung vermutet** |
| **J1** | USB-A | Funktion unbekannt | offen |
| **J18** | 2-polig | S0+ / S0- | Beschriftung geprüft |

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

- Funktion von J5 klären
- Funktion von J10 klären
- Funktion von J16 klären
- Funktion von J1 klären
- Zusammenhang zwischen J13 und J19 bei der 6-mA-DC-Überwachung klären
- Nutzung von J18 / S0 prüfen
