# E3/DC Multi Connect II Wallbox – Modbus TCP Register & direkte Steuerung

Dieses Projekt dokumentiert die nicht offiziell veröffentlichte **Modbus-TCP-Schnittstelle und die Modbus-Register der E3/DC Multi Connect II Wallbox (Multi Connect 2)**.

Ziel ist die **direkte Steuerung der Wallbox per Modbus TCP ohne E3/DC-Hauskraftwerk / EMC**. Dokumentiert werden unter anderem Register, Coils, Ladestatus, Ladestrom, Phasenumschaltung, RFID, interne Schnittstellen und getestete Schreibbefehle.

Die Ergebnisse stammen überwiegend aus eigenen Messungen und Funktionstests. Vermutungen und Fremdfunde werden ausdrücklich als solche gekennzeichnet.

## Dokumentation

Die eigentliche Register- und Funktionsdokumentation befindet sich hier:

- [Direkte Modbus-TCP-Register und Funktionen](docs/modbus-register.md)
- [Interne Steckverbinder und Anschlussbelegung](docs/stecker.md)
- [IP-Symcon Modbus-Vorlage](templates/E3DC_Multi_Connect_II_Modbus.json)

Diese README beschreibt den **Testaufbau, die verwendete Hardware, die Modbus-Grundlagen des Tests und die durchgeführten Adressscans**.

---

## Getestete Wallbox

**E3/DC Multi Connect II**

- Referenz / Typ: **XEV1K22T2E3DC**
- Mode 3
- Anschluss: **3P + N + PE**
- Nennstrom: **32 A**
- Spannung: **230 V 1~ / 400 V 3~**
- Leistungsklasse: **22 kW**
- Herstellungsdatum: **22.10.2024**
- Hersteller: **HagerEnergy GmbH**
- über Modbus ausgelesener Versionsstring: **7.0.5.0/1.0.2.0**

Die hier dokumentierten Ergebnisse beziehen sich auf genau dieses Testgerät bzw. diesen Firmwarestand.

---

## Verwendete Hardware und Software

### Prüfadapter

Verwendet wird ein **Gossen Metrawatt PRO-TYP II**.

Damit lassen sich die für die Tests benötigten Fahrzeugzustände reproduzierbar simulieren:

**CP – Control Pilot**

- A = kein Fahrzeug
- B = Fahrzeug angeschlossen, nicht ladebereit
- C = Fahrzeug angeschlossen und ladebereit
- D = Fahrzeug angeschlossen und ladebereit, Belüftung erforderlich
- E = Fehlerzustand

**PP – Proximity Pilot / Kabelstrom**

Am Prüfadapter wurden folgende Kabelströme simuliert:

- kein Kabel
- 13 A
- 20 A
- 32 A
- 63 A

Produktseite: [Gossen Metrawatt PRO-TYP II](https://www.gossenmetrawatt.de/produkte/pro-typ-i-i/)

### Lasttest

Für einen reproduzierbaren Lasttest wurde ein Wasserkocher mit ungefähr **2200 W** an der Prüfsteckdose des PRO-TYP II verwendet.

Zur Gegenmessung der von der Wallbox erfassten Phasenströme wird eine **BEHA AMPROBE AMP-310-EUR** Strommesszange verwendet.

Bei L1 wurden **8,24 A** mit der Strommesszange gemessen; gleichzeitig zeigte das zugeordnete Modbus-Register den Wert **82**. L2 und L3 wurden ebenfalls mit der Strommesszange gegengeprüft.

Die Detailzuordnung und Skalierung der Phasenstromregister wird in [docs/modbus-register.md](docs/modbus-register.md) geführt.

### S0-Energiezähler

Der Testaufbau wurde um einen **Eltako DSZ12D-3x65A** erweitert.

- Drehstromzähler 3x65 A
- S0-Ausgang
- **1000 Imp./kWh**
- S0-Ausgang potenzialfrei über Optokoppler
- Impulslänge laut Hersteller: **30 ms**

Der Zähler wird für die Untersuchung des internen S0-Anschlusses **J18** verwendet.

### RFID

Für den RFID-Test wurde eine Karte mit der aufgedruckten ID

```text
C08D62C4
```

verwendet.

Diese ID konnte vollständig in den Modbus-Registern wiedergefunden werden.


### Frontsensor / Kontakt-Sensor

An der Front befindet sich ein Kontakt-Sensor. Wird er etwa **1–4 Sekunden** betätigt, setzt die Wallbox **Coil 5 auf TRUE**. Ein weiterer Tastvorgang setzt Coil 5 nicht zurück. Wird Coil 5 per Modbus auf FALSE gesetzt, kann der Frontsensor ihn anschließend erneut auf TRUE setzen.

Die Hager-Anleitung beschreibt den Kontakt-Sensor bei aktiver Solaroptimierung als Funktion zum Beschleunigen des Ladevorgangs. Die genaue Rücksetzlogik und das Zusammenspiel mit Register 40083 sind noch offen.

### Interne Taster BP1

Auf beiden Platinen der Wallbox befindet sich jeweils ein Taster mit der Bezeichnung **BP1**.

Beide Taster wurden betätigt. Dabei wurde **keine beobachtbare Änderung** am Verhalten der Wallbox bzw. an den während des Tests überwachten Modbus-Werten festgestellt.

Die Funktion der beiden BP1-Taster ist damit weiterhin **unbekannt**.


### Interne Steckverbinder

Die geprüfte interne Verkabelung, bekannte Pinbelegungen und noch offene Steckverbinder sind auf einer eigenen Seite dokumentiert:

- [Interne Steckverbinder und Anschlussbelegung](docs/stecker.md)

### Software

Für die Untersuchung wurden verwendet:

- **Modbus Poll** für Adressscans sowie gezielte Lese-/Schreibtests
- **IP-Symcon** für die spätere praktische Einbindung und Schaltversuche
- Modbus TCP direkt zur Wallbox

---

## Modbus-Verbindung

Getestete Kommunikationsparameter:

- Modbus TCP
- TCP-Port: **502**
- Unit / Slave ID: **1**

### Adressierung

Modbus-PDU und IP-Symcon arbeiten bei den Holding Registern 0-basiert.

In der Registerdokumentation wird zur besseren Lesbarkeit die klassische **40001-Darstellung** verwendet.

| PDU / IP-Symcon | Dokumentation |
|---:|---:|
| 0 | 40001 |
| 80 | 40081 |
| 82 | 40083 |
| 101 | 40102 |

**40000 wird nicht als eigenes Register geführt.**  
PDU-Adresse 0 entspricht in dieser Dokumentation **40001**.

---

## Durchgeführte Adressscans

Am **02.10.2026** wurden mit Modbus Poll die vier klassischen Modbus-Lesebereiche jeweils von Adresse **0 bis 1000** gescannt.

### Zusammenfassung

| Funktion | Bereich mit gültiger Rückmeldung | Bereich ohne gültige Datenadresse |
|---|---|---|
| **FC01 – Read Coils** | **0–8** | 9–9999 → **02 Illegal Data Address** |
| **FC02 – Read Discrete Inputs** | 1–8 | 9–1000 → **02 Illegal Data Address** |
| **FC03 – Read Holding Registers** | 0–101, **9999–10031**, **14999–15125** | 102–9998, 10032–14998 und ab 15126 → **02 Illegal Data Address** |
| **FC04 – Read Input Registers** | keiner | 0–1000 → **02 Illegal Data Address** |

### FC01 – Read Coils

Beim ersten Scan wurden folgende Werte gelesen:

```text
Adresse: 0 1 2 3 4 5 6 7 8
Wert:    1 1 1 0 0 0 0 1 0
```

Die Adressen **0–8** sind lesbar. Ab Adresse **9** wurde im erweiterten Scan bis **9999** durchgehend **02 Illegal Data Address** zurückgegeben.

Die Funktionszuordnung der einzelnen Coils wird auf der [Modbus-Seite](docs/modbus-register.md) dokumentiert.

### FC02 – Read Discrete Inputs

Der erste Scan ergab im gleichen Zustand exakt dasselbe Bitmuster wie FC01:

```text
Adresse: 1 2 3 4 5 6 7 8
FC01:    1 1 0 0 0 0 1 0
FC02:    1 1 0 0 0 0 1 0
```

Anschließend wurden die Zustände **1–8 bei mehreren Änderungen der Coils geprüft**. Dabei spiegelten die Discrete Inputs **immer exakt die entsprechenden Coils 1–8**.

Damit ist für das getestete Gerät verifiziert:

```text
Discrete Input 1 = Coil 1
Discrete Input 2 = Coil 2
...
Discrete Input 8 = Coil 8
```

FC02 liefert damit im geprüften Bereich **keine zusätzlichen Zustandsinformationen gegenüber FC01**. Für die praktische IP-Symcon-Anbindung wird FC02 deshalb nicht benötigt; die Ergebnisse bleiben hier als dokumentierte Eigenschaft der Wallbox erhalten.

### FC03 – Read Holding Registers

Die erweiterten Scans ergaben drei getrennte gültige Bereiche:

| PDU-Adresse | 40001-Darstellung | Ergebnis |
|---:|---:|---|
| 0–101 | 40001–40102 | **Response ok** |
| 102–9998 | 40103–49999 | **02 Illegal Data Address** |
| **9999–10031** | **50000–50032** | **Response ok** |
| 10032–14998 | 50033–54999 | **02 Illegal Data Address** |
| **14999–15125** | **55000–55126** | **Response ok** |
| 15126–30000 | 55127–70001 | **02 Illegal Data Address** |

Die Untergrenze des zweiten Blocks wurde separat gegengeprüft:

- PDU **9998** → **02 Illegal Data Address**
- PDU **9999** → **Response ok**

Damit existieren zusätzlich zum Basisbereich zwei weitere FC03-Blöcke bei **50000–50032** und **55000–55126**. Im Block 55000–55126 war beim Scan nur Register **55001 = 8**, alle übrigen Werte waren 0.

Der anschließende FC03-Scan von **PDU 20000 bis 30000** (Holding Register **60001–70001**) ergab vollständig **02 Illegal Data Address**; in diesem Bereich wurde kein weiterer Registerblock gefunden.

### FC04 – Read Input Registers

Im gesamten Bereich **0–1000** wurde kein gültiges Input Register gefunden.

Alle Adressen liefern:

```text
02 Illegal Data Address
```

---

## Verwendete Modbus-Funktionen

Im bisherigen Test wurden verwendet:

- **FC01** – Read Coils
- **FC02** – Read Discrete Inputs
- **FC03** – Read Holding Registers
- **FC04** – Read Input Registers
- **FC05** – Write Single Coil
- **FC06** – Write Single Register
- **FC16** – Write Multiple Registers

Welche Adressen tatsächlich schreibbar sind und welche Funktion dahinter steckt, wird ausschließlich in [docs/modbus-register.md](docs/modbus-register.md) geführt.

---

## Kennzeichnung der Ergebnisse

In der Detaildokumentation werden die Ergebnisse so gekennzeichnet:

- **Verifiziert** – am eigenen Gerät reproduzierbar getestet
- **Hypothese** – technisch plausibel, aber noch nicht vollständig gegengeprüft
- **Fremdquelle** – Bedeutung stammt aus einer externen Quelle und wurde noch nicht vollständig selbst bestätigt
- **RO** – lesbar, Schreibzugriff wurde abgewiesen bzw. nicht vorgesehen
- **RW** – Lesen und Schreiben wurden praktisch bestätigt

---

## Quellen zum Testaufbau

### Hager / E3/DC

[Hager Installationsanleitung witty solar XEV1K22T2S / XEV1K07T2S](https://assets.hager.com/step-content/P/HA_39675796/Document/std.lang.all/6LE009000B_XEV1KXX2S_EVCS_WITTY-SOLAR_MANUAL_DE_WEB.pdf)

Verwendet unter anderem für die Einordnung des I-Max-Drehkodierschalters und der Stromstufen.

### Prüfadapter

[Gossen Metrawatt PRO-TYP II](https://www.gossenmetrawatt.de/produkte/pro-typ-i-i/)

Verwendet für CP-Zustände, PP-Kabelsimulation und reproduzierbare Fahrzeugsimulation.

---

## Hinweis

Es handelt sich um **Reverse Engineering einer nicht offiziell dokumentierten direkten Modbus-Schnittstelle**. Die beschriebenen Funktionen beziehen sich auf das oben genannte Testgerät und den getesteten Firmwarestand. Andere Firmwarestände oder Hardwarevarianten können sich anders verhalten.


---

## Lizenz

Dieses Projekt ist ausdrücklich zum **Nachbauen, Weiterentwickeln, Forken und Verbessern** gedacht.

Die Nutzung, Änderung und Weitergabe ist für **nicht-kommerzielle Zwecke** erlaubt. Kommerzielle Nutzung, Verkauf, entgeltliche Dienstleistungen oder die Integration in kommerzielle Produkte und Angebote sind ohne separate schriftliche Erlaubnis nicht gestattet.

Weiterentwicklungen und Forks sind ausdrücklich erwünscht. Bei einer Weitergabe oder Veröffentlichung von Änderungen müssen der ursprüngliche Urheberhinweis und diese Lizenz erhalten bleiben.

Die vollständigen Bedingungen stehen in der Datei [LICENSE](LICENSE).
