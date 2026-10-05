# Interne Steckverbinder und Anschlussbelegung

Diese Seite dokumentiert ausschließlich die bei der **E3/DC Multi Connect II** vorgefundenen internen Steckverbinder, deren geprüfte Verdrahtung und noch offene Zuordnungen.

Die Angaben beziehen sich auf das im Projekt untersuchte Gerät. Verifizierte Verdrahtungen werden von Hypothesen ausdrücklich getrennt.

## Hauptplatine – Steckerübersicht

<p align="center">
  <img src="../pictures/E3DC%20Multi%20Connect%20II%20-%20Hauptplatine%20Stecker%20beschriftet.jpg" alt="E3/DC Multi Connect II – Hauptplatine mit beschrifteten Steckverbindern" width="100%">
</p>

---

## Übersicht

| Stecker | Pins / Kontakte | Zuordnung | Status |
|---|---:|---|---|
| **J1** | USB-A | Funktion unbekannt | offen |
| **J2** | 10-polig | Flachbandkabel zur Nebenplatine, dort ebenfalls **J2** | **Verdrahtung geprüft** |
| **J3** | 10-polig | Flachbandkabel zur Nebenplatine, dort ebenfalls **J3** | **Verdrahtung geprüft** |
| **J5** | 3-polig | nicht belegt, Funktion unbekannt | offen |
| **J8** | 5-polig | Ansteuerung 2-poliger Schütz Benedikt R40-20 230 für L2/L3 | **Verdrahtung geprüft** |
| **J9** | 6-polig | Stromwandler L1 / L2 / L3 | **Verdrahtung geprüft** |
| **J10** | 6-polig | Funktion unbekannt | offen |
| **J12** | 5-polig | PP, CP, PE und Motor Steckerverriegelung | **Verdrahtung geprüft** |
| **J13** | 6-polig | laut mitgelieferter Anleitung Sensoranschluss 6 mA | Dokumentationshinweis |
| **J14** | 2-polig | Spannungsrückmeldung L1 hinter dem Ausgangsschütz; sehr wahrscheinlich Schützklebeüberwachung | **Verdrahtung geprüft; Funktion durch Fehler-Simulation stark gestützt** |
| **J15** | 2-polig | N / L vom 2-poligen C16-Automaten Hager NFT716 | **Verdrahtung geprüft** |
| **J16** | 4-polig | Pins 30–33, Funktion unbekannt; elektrische Testreihe dokumentiert | **Zuordnung offen / weitere Tests gestoppt** |
| **J17** | 3-polig | Spulenanschluss des 3-poligen Ausgangsschützes | **Verdrahtung geprüft, Pin 3 offen** |
| **J18** | 2-polig | S0+ / S0- | Beschriftung geprüft |
| **J19** | 4-polig | Summenstromwandler; L1, L2, L3 und N werden gemeinsam durchgeführt | **Hardware beobachtet; 6-mA-DC-Überwachung vermutet** |
| **ohne J-Beschriftung** | 8-polig | Funktion unbekannt | offen |

---


## J1 – USB-A

- **Nicht belegt**
- Steckertyp: **USB-A**
- Funktion bisher unbekannt

### USB-Stick-Test

Für einen Funktionstest wurde ein **FAT32-formatierter USB-Stick mit Aktivitäts-LED** an J1 angeschlossen.

Beobachtungen:

- die LED des USB-Sticks leuchtet dauerhaft, der Anschluss stellt also Versorgungsspannung bereit
- während des Tests wurde **kein Blinken der Aktivitäts-LED** beobachtet, das auf einen längeren Lese- oder Schreibzugriff hindeuten würde
- Betätigung von **BP1 auf der Hauptplatine** und **BP1 auf der Netzwerkplatine** einzeln sowie gemeinsam führte zu keiner erkennbaren USB-Aktivität
- auch nach einem **Neustart der Wallbox** wurde keine USB-Aktivität beobachtet
- der Test wurde zusätzlich mit bereits eingestecktem USB-Stick während des Bootvorgangs durchgeführt
- auf dem USB-Stick wurden **keine Dateien oder Verzeichnisse angelegt**
- zusätzlich wurden die bisher bekannten schaltbaren Modbus-Werte testweise aktiviert, um gegebenenfalls eine Log-, Export- oder Servicefunktion auszulösen; auch dabei wurde keine USB-Aktivität festgestellt

Ein sehr kurzer Zugriff, der von der Aktivitäts-LED des verwendeten Sticks nicht sichtbar angezeigt wird, kann mit diesem Test nicht vollständig ausgeschlossen werden.

**Ergebnis:** J1 liefert USB-Versorgung, eine Daten-, Log- oder Exportfunktion konnte bisher jedoch nicht nachgewiesen werden.

**Hinweis zu BP1:** Die inzwischen nachgewiesene Funktion von **BP1 auf der Netzwerkplatine** ist eine Netzwerk-Rücksetzung / Reaktivierung durch etwa **10 Sekunden gedrückt halten**. Ein Zusammenhang dieses Tasters mit J1/USB konnte weiterhin nicht festgestellt werden. Die Funktion von **BP1 auf der Hauptplatine** bleibt unbekannt.

---


## J2 – Flachbandkabel zur Nebenplatine

- **Belegt**
- **10-polig**
- Flachbandkabel zur Nebenplatine
- dort ebenfalls auf **J2** angeschlossen
- Verdrahtung geprüft

---


## J3 – Flachbandkabel zur Nebenplatine

- **Belegt**
- **10-polig**
- Flachbandkabel zur Nebenplatine
- dort ebenfalls auf **J3** angeschlossen
- Verdrahtung geprüft

---


## J5

- **Nicht belegt**
- **3-polig**
- Funktion bisher unbekannt

---


## J8 – 2-poliger Schütz L2/L3

- **Belegt**

Die Verkabelung wurde geprüft.

Der zugehörige Schütz ist ein **Benedikt R40-20 230**. Über diesen 2-poligen Schütz werden **L2 und L3** geführt.

| Kontakt | Beschriftung | Leitung / Zuordnung |
|---:|---|---|
| **1** | D/N | frei |
| **2** | D/N | frei |
| **3** | NST | Blau → **A1** des 2-poligen Schützes |
| **4** | keine Beschriftung | frei |
| **5** | LST | Gelb → **A2** des 2-poligen Schützes |

### Test bei abgezogenem J8

J8 wurde abgezogen und anschließend ein Ladevorgang gestartet.

Beobachtung:

- der Ladevorgang lässt sich weiterhin starten
- es wurde **kein Fehler / Fehlercode** ausgelöst

Damit ist bisher keine Überwachung des fehlenden J8-Anschlusses nachgewiesen.

---


## J9 – Stromwandler

- **Belegt**

Die Verkabelung wurde geprüft.

Alle sechs Leitungen am Stecker sind **grau**.

| Kontakt | Zuordnung |
|---:|---|
| **10 / 11** | Stromwandler **L1** |
| **12 / 13** | Stromwandler **L2** |
| **14 / 15** | Stromwandler **L3** |

Die drei Stromwandler passen zu den im Modbus gefundenen Phasenstromwerten 40072–40074.

---


## J10

- **Nicht belegt**
- **6-polig**
- Funktion bisher unbekannt

---


## J12 – Fahrzeuganschluss / Steckerverriegelung

- **Belegt**

Die Verkabelung wurde geprüft.

| Kontakt | Funktion | Leitungsfarbe |
|---:|---|---|
| **20** | PP – Proximity Pilot | Lila |
| **21** | CP – Control Pilot | Orange |
| **22** | PE – Schutzleiter | Grün/Gelb |
| **23** | Motor Steckerverriegelung **+** | Rot |
| **24** | Motor Steckerverriegelung **-** | Schwarz |

---


## J13

- **Nicht belegt**
- **6-polig**
- laut der mitgelieferten Anleitung als **Sensoranschluss 6 mA** bezeichnet

Die genaue interne Zuordnung wurde bisher nicht elektrisch nachverfolgt.

---


## J14 – Spannungsrückmeldung L1 / Schützklebeüberwachung

- **Belegt**

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

Die Betriebsanleitung nennt eine integrierte **Schützklebeüberwachung**. In Abschnitt 9.6.2 wird **rotes Dauerlicht** unter anderem mit „Der 40 A-Schütz arbeitet nicht“ beschrieben. Die Zuordnung zu J14 ergibt sich zusätzlich aus diesem eigenen Funktionstest.

### Rücksetzverhalten

Der erzeugte Fehler blieb **verriegelt**:

- Rückkehr durch normale CP-/Ladezustände: **nicht möglich**
- Rücksetzen über beobachtete Modbus-Werte/Coils: **nicht möglich**
- Entfernen der Brücke allein: **kein Reset**
- erst **Steuersicherung AUS / EIN** setzte den Fehler zurück

Nach dem Neustart:

- **40085: 130 → 0**
- **Coil 8: TRUE → FALSE**
- rote Daueranzeige erloschen

Damit verhält sich die Schützüberwachung wie eine sicherheitsgerichtete, bis zum Spannungsreset verriegelte Störung.

---


## J15 – Versorgung vom Leitungsschutzschalter

- **Belegt**

Die Verkabelung wurde geprüft.

J15 ist mit dem **2-poligen C16-Automaten Hager NFT716** verbunden.

| Kontakt | Leitung / Zuordnung |
|---:|---|
| **1** | **N**, Blau |
| **2** | **L**, Braun, vom 2-poligen C16-Automaten |

---


## J16 – unbelegter 4-poliger Anschluss

- **Nicht belegt**

J16 ist **4-polig** und bei der untersuchten Multi Connect II **nicht belegt**.  
Die Kontakte sind auf der Platine mit **30–33** bezeichnet.

Ein passender Gegenstecker steht nicht zur Verfügung. Die Kontaktierung bei den Versuchen erfolgte daher provisorisch. Insbesondere die Versuche mit Widerständen sind deshalb **mit Vorsicht zu bewerten**.

### Widerstandsmessungen – Wallbox spannungsfrei

| Messung | Ergebnis |
|---|---:|
| **30 → PE** | ca. **30 kΩ**, langsam steigend; Messung bei ca. **37,4 kΩ** beendet |
| **31 → PE** | ca. **4,79 kΩ** |
| **32 → PE** | gleiches Verhalten wie Pin 30 |
| **33 → PE** | ca. **4,79 kΩ** |
| **30 ↔ 32** | **0 Ω** |
| **31 ↔ 33** | ca. **9,59 kΩ** |
| **30 ↔ 31** | kein Durchgang |
| **32 ↔ 33** | kein Durchgang |

Damit sind **Pin 30 und Pin 32 elektrisch direkt miteinander verbunden**. Pin 31 und Pin 33 zeigen dagegen ein weitgehend identisches Verhalten.

### Diodentest – Wallbox spannungsfrei

| Messrichtung | Ergebnis |
|---|---:|
| **30 → 31** | **OL** |
| **31 → 30** | ca. **1,28 V** |
| **32 → 33** | **OL** |
| **33 → 32** | ca. **1,28 V** |
| **31 → PE** | Anzeige ca. **0,0 V** |
| **PE → 31** | ca. **0,8 V** |
| **33 ↔ PE** | gleiches Verhalten wie Pin 31 |

Das identische Verhalten der beiden Paare 30/31 und 32/33 spricht für zwei ähnlich aufgebaute Schaltungskanäle. Eine konkrete Funktion lässt sich daraus nicht sicher ableiten.

### Spannungsmessung – Wallbox eingeschaltet

Zwischen den jeweiligen Pinpaaren wurden ungefähr **11,6 V DC** gemessen:

- **30 / 31:** ca. **11,6 V DC**
- **32 / 33:** ca. **11,6 V DC**
- bei der Messung 30/31 war **Pin 30 gegenüber Pin 31 positiv**

Zusammen mit der direkten Verbindung zwischen Pin 30 und Pin 32 deutet dies auf einen gemeinsamen Anschluss und zwei ähnlich beschaltete Gegenkontakte hin. Diese Interpretation ist jedoch **nicht verifiziert**.

### Versuche mit Serienwiderstand

Zur vorsichtigen Prüfung einer möglichen Schalt-/Eingangsfunktion wurden die jeweiligen Kontakte provisorisch über Serienwiderstände verbunden. Der Widerstand wurde schrittweise bis auf **680 Ω** reduziert.

Dabei wurde:

- keine eindeutige Änderung an den bisher beobachteten Modbus-Registern festgestellt
- keine eindeutige sichtbare Funktionsänderung der Wallbox festgestellt

Auf einen direkten Kurzschluss bzw. weitere Versuche mit niedrigeren Widerständen wurde bewusst verzichtet.

Da **kein passender Stecker vorhanden** ist und die Kontaktierung nur provisorisch erfolgen konnte, sind diese Ergebnisse **nicht als sicherer Funktionstest** zu bewerten.

### Bewertung

Die ursprüngliche Vermutung eines Anschlusses für **L1 / L2 / L3 / N** passt nicht zu den Messungen, da Pin 30 und Pin 32 direkt miteinander verbunden sind und zwischen den Pinpaaren nur etwa 11,6 V DC anliegen.

Aufgrund der zwei ähnlich aufgebauten Kanäle wäre grundsätzlich eine optionale externe Schaltfunktion denkbar. Als mögliche Erklärung wurde ein Anschluss für den **Schlüsselschalter einer anderen Wallboxvariante, z. B. Multi Connect 1**, betrachtet. Dafür liegt jedoch **kein Nachweis** vor.

**Status:** Funktion unbekannt. Weitere elektrische Tests wurden beendet, um eine Beschädigung der Wallbox zu vermeiden. Die Klärung bleibt ein **Nice-to-have**.

---


## J17 – Spule 3-poliger Ausgangsschütz

- **Belegt**

Die Verkabelung wurde geprüft.

| Kontakt | Beschriftung | Leitung / Zuordnung |
|---:|---|---|
| **1** | KM3 | Gelb → **A2** des 3-poligen Schützes |
| **2** | N | Blau → **A1** des 3-poligen Schützes |
| **3** | — | noch nicht zugeordnet |

### Test bei abgezogenem J17

J17 wurde abgezogen und anschließend ein Ladevorgang gestartet.

Beobachtung:

- der Ladevorgang lässt sich weiterhin starten
- es wurde **kein Fehler / Fehlercode** ausgelöst

Damit ist bisher keine Überwachung des fehlenden J17-Anschlusses nachgewiesen.

---


## J18 – S0

- **Belegt**

J18 ist **2-polig** beschriftet mit:

| Kontakt | Funktion |
|---|---|
| **S0+** | S0 Plus |
| **S0-** | S0 Minus |

Zwischen **S0+ und S0-** wurden an der Wallbox im Leerlauf ungefähr **11,6 V DC** gemessen.

Für den Test wurde ein **Eltako DSZ12D-3x65A** mit potenzialfreiem S0-Optokopplerausgang angeschlossen:

- **1000 Imp./kWh**
- Impulslänge laut Hersteller **30 ms**
- zulässige externe S0-Spannung laut Hersteller **5…30 V DC**
- max. **20 mA**

Damit liegt die von J18 bereitgestellte Spannung von ca. 11,6 V innerhalb des zulässigen Bereichs des Eltako-S0-Ausgangs.

Anschluss für den Test:

- **J18 S0+ → Eltako S0+**
- **J18 S0- → Eltako S0-**

Nach zwei Aufheizvorgängen mit dem Wasserkocher wurde trotz angeschlossenem S0-Ausgang **keine Änderung in den bisher bekannten Modbus-Registern 40001–40102 beobachtet**.

Noch offen ist, ob J18 intern nur für eine andere Funktion verwendet wird, ob ein bislang unbekannter Zähler außerhalb des bekannten Registerbereichs existiert oder ob eine zusätzliche Konfiguration/Freigabe notwendig ist.

---


## J19 – Summenstromwandler

- **Belegt**

J19 ist **4-polig** und führt zum Summenstromwandler.

Durch den Wandler werden gemeinsam geführt:

- L1
- L2
- L3
- N

Aufgrund des Aufbaus liegt die Vermutung nahe, dass dieser Wandler zur **6-mA-DC-Fehlerstromüberwachung** gehört.

Diese Funktionszuordnung ist aktuell eine **Hypothese** und noch nicht elektrisch bzw. über einen gezielten Fehlerstromtest bestätigt.

---


## 8-poliger Stecker ohne J-Beschriftung

- **Nicht belegt**
- **8-polig**
- keine J-Beschriftung vorhanden
- Funktion bisher unbekannt

---

## Offene Punkte

- J17 Pin 3 zuordnen
- Funktion von J5 klären
- Funktion von J10 klären
- Funktion von J16 klären
- Funktion des **8-poligen Steckers ohne J-Beschriftung** klären
- Funktion von J1 klären
- Zusammenhang zwischen J13 und J19 bei der 6-mA-DC-Überwachung klären
- Nutzung von J18 / S0 prüfen
