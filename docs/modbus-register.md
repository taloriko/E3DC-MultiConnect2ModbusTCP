# Direkte Modbus-TCP-Register der E3/DC Multi Connect II

> Reverse-Engineering-Dokumentation für den direkten Zugriff auf die Wallbox **ohne E3/DC-Hauskraftwerk**.  
> Testgerät: **E3/DC Multi Connect II, Referenz XEV1K22T2E3DC**, 22 kW / 32 A, Baujahr 22.10.2024.

## Stand

**Stand der Messungen: 02.10.2026**

Die Wallbox antwortet direkt per **Modbus TCP**. Verwendet wurde bisher:

- TCP Port **502**
- Lesen: **FC03 – Read Holding Registers**
- Schreiben: **FC06 – Write Single Register** und **FC16 – Write Multiple Registers**
- Registerdarstellung in dieser Doku: **40001-basiert**
- In IP-Symcon entspricht deshalb:
  - 40001 → Adresse 0
  - 40081 → Adresse 80
  - 40083 → Adresse 82
  - 40102 → Adresse 101

**40000 wird nicht als eigenes Register geführt.** In der hier verwendeten klassischen 4xxxx-Notation beginnt der Bereich bei 40001; der Modbus-PDU-Offset 0 entspricht 40001.

Eigene Leseversuche liefern gültige Antworten bis einschließlich **40102**. Register oberhalb 40102 werden separat weiter untersucht.

## Testaufbau

### Wallbox

- E3/DC Multi Connect II
- Referenz: XEV1K22T2E3DC
- 230 V 1~ / 400 V 3~
- 32 A
- Versionsstring aus Modbus: **7.0.5.0/1.0.2.0**
- Betrieb im LAN ohne Hauskraftwerk/EMC

### Fahrzeugsimulation

Verwendet wurde ein **Gossen Metrawatt PRO-TYP II**.

CP-Simulation nach Herstellerbeschreibung:

| CP | Bedeutung |
|---|---|
| A | kein Fahrzeug angeschlossen |
| B | Fahrzeug angeschlossen, nicht ladebereit |
| C | Fahrzeug angeschlossen, ladebereit, keine Belüftung gefordert |
| D | Fahrzeug angeschlossen, ladebereit, Belüftung gefordert |
| E | Fehlerzustand / CP–PE-Kurzschluss über interne Diode |

PP-Simulation:

| PP | Widerstand laut PRO-TYP II |
|---|---:|
| kein Kabel | 0 Ω laut Gerätebeschreibung |
| 13 A | 1,5 kΩ |
| 20 A | 680 Ω |
| 32 A | 220 Ω |
| 63 A | 100 Ω |

Zusätzlich wurden getestet:

- 1-phasiger **2200-W-Wasserkocher** an L1 des Prüfadapters
- RFID-Karte mit aufgedruckter ID **C08D62C4**
- alle Positionen des internen **I-Max-Drehkodierschalters**

## Statuskennzeichnung

- **verifiziert**: Verhalten wurde am eigenen Gerät reproduzierbar gemessen.
- **Hypothese**: Messwert passt plausibel, ist aber noch nicht abschließend durch Gegenprobe bestätigt.
- **Fremdquelle**: Bedeutung stammt aus einer externen Quelle und wurde noch nicht funktional vollständig selbst bestätigt.
- **RO**: Schreibversuch wurde abgewiesen, sofern dies in der Tabelle explizit mit FC06/FC16 angegeben ist.
- **R; Schreiben nicht getestet**: nur gelesen; daraus darf keine Aussage über Schreibbarkeit abgeleitet werden.
- **RW verifiziert**: Schreiben wurde angenommen und zurückgelesen.

## Registerübersicht

| Register | Aufbau | Zugriff | Bedeutung | Beobachtung / Werte | Konkreter Test | Herkunft |
|---:|---|---|---|---|---|---|
| 40001 | UINT16 / HEX | R; Schreiben nicht getestet | Kennung / Magic | 58332 = 0xE3DC | Baseline direkt nach Modbus-Verbindung | EIGEN |
| 40002 | UINT16 | R; FC06 abgewiesen, FC16 noch nicht geprüft | Unbekannt | im Grundzustand 1 | Lesen; später FC06-Schreibtest | EIGEN |
| 40003 | UINT16 | R; FC06 abgewiesen, FC16 noch nicht geprüft | Unbekannt | im Grundzustand 0 | Lesen; später FC06-Schreibtest | EIGEN |
| 40004 | 2 Byte ASCII | R; Schreiben nicht getestet | Herstellerstring, Bytes 1–2 | Gesamtstring 40004–40019 = `HAGER` + NUL-Padding | Einzelregister zusätzlich als Text dekodiert | EIGEN |
| 40005 | 2 Byte ASCII | R; Schreiben nicht getestet | Herstellerstring, Bytes 3–4 | Gesamtstring 40004–40019 = `HAGER` + NUL-Padding | Einzelregister zusätzlich als Text dekodiert | EIGEN |
| 40006 | 2 Byte ASCII | R; Schreiben nicht getestet | Herstellerstring, Bytes 5–6 | Gesamtstring 40004–40019 = `HAGER` + NUL-Padding | Einzelregister zusätzlich als Text dekodiert | EIGEN |
| 40007 | 2 Byte ASCII | R; Schreiben nicht getestet | Herstellerstring, Bytes 7–8 | Gesamtstring 40004–40019 = `HAGER` + NUL-Padding | Einzelregister zusätzlich als Text dekodiert | EIGEN |
| 40008 | 2 Byte ASCII | R; Schreiben nicht getestet | Herstellerstring, Bytes 9–10 | Gesamtstring 40004–40019 = `HAGER` + NUL-Padding | Einzelregister zusätzlich als Text dekodiert | EIGEN |
| 40009 | 2 Byte ASCII | R; Schreiben nicht getestet | Herstellerstring, Bytes 11–12 | Gesamtstring 40004–40019 = `HAGER` + NUL-Padding | Einzelregister zusätzlich als Text dekodiert | EIGEN |
| 40010 | 2 Byte ASCII | R; Schreiben nicht getestet | Herstellerstring, Bytes 13–14 | Gesamtstring 40004–40019 = `HAGER` + NUL-Padding | Einzelregister zusätzlich als Text dekodiert | EIGEN |
| 40011 | 2 Byte ASCII | R; Schreiben nicht getestet | Herstellerstring, Bytes 15–16 | Gesamtstring 40004–40019 = `HAGER` + NUL-Padding | Einzelregister zusätzlich als Text dekodiert | EIGEN |
| 40012 | 2 Byte ASCII | R; Schreiben nicht getestet | Herstellerstring, Bytes 17–18 | Gesamtstring 40004–40019 = `HAGER` + NUL-Padding | Einzelregister zusätzlich als Text dekodiert | EIGEN |
| 40013 | 2 Byte ASCII | R; Schreiben nicht getestet | Herstellerstring, Bytes 19–20 | Gesamtstring 40004–40019 = `HAGER` + NUL-Padding | Einzelregister zusätzlich als Text dekodiert | EIGEN |
| 40014 | 2 Byte ASCII | R; Schreiben nicht getestet | Herstellerstring, Bytes 21–22 | Gesamtstring 40004–40019 = `HAGER` + NUL-Padding | Einzelregister zusätzlich als Text dekodiert | EIGEN |
| 40015 | 2 Byte ASCII | R; Schreiben nicht getestet | Herstellerstring, Bytes 23–24 | Gesamtstring 40004–40019 = `HAGER` + NUL-Padding | Einzelregister zusätzlich als Text dekodiert | EIGEN |
| 40016 | 2 Byte ASCII | R; Schreiben nicht getestet | Herstellerstring, Bytes 25–26 | Gesamtstring 40004–40019 = `HAGER` + NUL-Padding | Einzelregister zusätzlich als Text dekodiert | EIGEN |
| 40017 | 2 Byte ASCII | R; Schreiben nicht getestet | Herstellerstring, Bytes 27–28 | Gesamtstring 40004–40019 = `HAGER` + NUL-Padding | Einzelregister zusätzlich als Text dekodiert | EIGEN |
| 40018 | 2 Byte ASCII | R; Schreiben nicht getestet | Herstellerstring, Bytes 29–30 | Gesamtstring 40004–40019 = `HAGER` + NUL-Padding | Einzelregister zusätzlich als Text dekodiert | EIGEN |
| 40019 | 2 Byte ASCII | R; Schreiben nicht getestet | Herstellerstring, Bytes 31–32 | Gesamtstring 40004–40019 = `HAGER` + NUL-Padding | Einzelregister zusätzlich als Text dekodiert | EIGEN |
| 40020 | 2 Byte ASCII | R; Schreiben nicht getestet | Produktkennung, Bytes 1–2 | Gesamtstring 40020–40035 = `XEVFR` + NUL-Padding | Einzelregister zusätzlich als Text dekodiert | EIGEN |
| 40021 | 2 Byte ASCII | R; Schreiben nicht getestet | Produktkennung, Bytes 3–4 | Gesamtstring 40020–40035 = `XEVFR` + NUL-Padding | Einzelregister zusätzlich als Text dekodiert | EIGEN |
| 40022 | 2 Byte ASCII | R; Schreiben nicht getestet | Produktkennung, Bytes 5–6 | Gesamtstring 40020–40035 = `XEVFR` + NUL-Padding | Einzelregister zusätzlich als Text dekodiert | EIGEN |
| 40023 | 2 Byte ASCII | R; Schreiben nicht getestet | Produktkennung, Bytes 7–8 | Gesamtstring 40020–40035 = `XEVFR` + NUL-Padding | Einzelregister zusätzlich als Text dekodiert | EIGEN |
| 40024 | 2 Byte ASCII | R; Schreiben nicht getestet | Produktkennung, Bytes 9–10 | Gesamtstring 40020–40035 = `XEVFR` + NUL-Padding | Einzelregister zusätzlich als Text dekodiert | EIGEN |
| 40025 | 2 Byte ASCII | R; Schreiben nicht getestet | Produktkennung, Bytes 11–12 | Gesamtstring 40020–40035 = `XEVFR` + NUL-Padding | Einzelregister zusätzlich als Text dekodiert | EIGEN |
| 40026 | 2 Byte ASCII | R; Schreiben nicht getestet | Produktkennung, Bytes 13–14 | Gesamtstring 40020–40035 = `XEVFR` + NUL-Padding | Einzelregister zusätzlich als Text dekodiert | EIGEN |
| 40027 | 2 Byte ASCII | R; Schreiben nicht getestet | Produktkennung, Bytes 15–16 | Gesamtstring 40020–40035 = `XEVFR` + NUL-Padding | Einzelregister zusätzlich als Text dekodiert | EIGEN |
| 40028 | 2 Byte ASCII | R; Schreiben nicht getestet | Produktkennung, Bytes 17–18 | Gesamtstring 40020–40035 = `XEVFR` + NUL-Padding | Einzelregister zusätzlich als Text dekodiert | EIGEN |
| 40029 | 2 Byte ASCII | R; Schreiben nicht getestet | Produktkennung, Bytes 19–20 | Gesamtstring 40020–40035 = `XEVFR` + NUL-Padding | Einzelregister zusätzlich als Text dekodiert | EIGEN |
| 40030 | 2 Byte ASCII | R; Schreiben nicht getestet | Produktkennung, Bytes 21–22 | Gesamtstring 40020–40035 = `XEVFR` + NUL-Padding | Einzelregister zusätzlich als Text dekodiert | EIGEN |
| 40031 | 2 Byte ASCII | R; Schreiben nicht getestet | Produktkennung, Bytes 23–24 | Gesamtstring 40020–40035 = `XEVFR` + NUL-Padding | Einzelregister zusätzlich als Text dekodiert | EIGEN |
| 40032 | 2 Byte ASCII | R; Schreiben nicht getestet | Produktkennung, Bytes 25–26 | Gesamtstring 40020–40035 = `XEVFR` + NUL-Padding | Einzelregister zusätzlich als Text dekodiert | EIGEN |
| 40033 | 2 Byte ASCII | R; Schreiben nicht getestet | Produktkennung, Bytes 27–28 | Gesamtstring 40020–40035 = `XEVFR` + NUL-Padding | Einzelregister zusätzlich als Text dekodiert | EIGEN |
| 40034 | 2 Byte ASCII | R; Schreiben nicht getestet | Produktkennung, Bytes 29–30 | Gesamtstring 40020–40035 = `XEVFR` + NUL-Padding | Einzelregister zusätzlich als Text dekodiert | EIGEN |
| 40035 | 2 Byte ASCII | R; Schreiben nicht getestet | Produktkennung, Bytes 31–32 | Gesamtstring 40020–40035 = `XEVFR` + NUL-Padding | Einzelregister zusätzlich als Text dekodiert | EIGEN |
| 40036 | 2 Byte ASCII | R; Schreiben nicht getestet | Gerätekennung, Bytes 1–2 | 32-Byte-Gerätekennung; gerätespezifisch | Einzelregister zusätzlich als Text dekodiert | EIGEN |
| 40037 | 2 Byte ASCII | R; Schreiben nicht getestet | Gerätekennung, Bytes 3–4 | 32-Byte-Gerätekennung; gerätespezifisch | Einzelregister zusätzlich als Text dekodiert | EIGEN |
| 40038 | 2 Byte ASCII | R; Schreiben nicht getestet | Gerätekennung, Bytes 5–6 | 32-Byte-Gerätekennung; gerätespezifisch | Einzelregister zusätzlich als Text dekodiert | EIGEN |
| 40039 | 2 Byte ASCII | R; Schreiben nicht getestet | Gerätekennung, Bytes 7–8 | 32-Byte-Gerätekennung; gerätespezifisch | Einzelregister zusätzlich als Text dekodiert | EIGEN |
| 40040 | 2 Byte ASCII | R; Schreiben nicht getestet | Gerätekennung, Bytes 9–10 | 32-Byte-Gerätekennung; gerätespezifisch | Einzelregister zusätzlich als Text dekodiert | EIGEN |
| 40041 | 2 Byte ASCII | R; Schreiben nicht getestet | Gerätekennung, Bytes 11–12 | 32-Byte-Gerätekennung; gerätespezifisch | Einzelregister zusätzlich als Text dekodiert | EIGEN |
| 40042 | 2 Byte ASCII | R; Schreiben nicht getestet | Gerätekennung, Bytes 13–14 | 32-Byte-Gerätekennung; gerätespezifisch | Einzelregister zusätzlich als Text dekodiert | EIGEN |
| 40043 | 2 Byte ASCII | R; Schreiben nicht getestet | Gerätekennung, Bytes 15–16 | 32-Byte-Gerätekennung; gerätespezifisch | Einzelregister zusätzlich als Text dekodiert | EIGEN |
| 40044 | 2 Byte ASCII | R; Schreiben nicht getestet | Gerätekennung, Bytes 17–18 | 32-Byte-Gerätekennung; gerätespezifisch | Einzelregister zusätzlich als Text dekodiert | EIGEN |
| 40045 | 2 Byte ASCII | R; Schreiben nicht getestet | Gerätekennung, Bytes 19–20 | 32-Byte-Gerätekennung; gerätespezifisch | Einzelregister zusätzlich als Text dekodiert | EIGEN |
| 40046 | 2 Byte ASCII | R; Schreiben nicht getestet | Gerätekennung, Bytes 21–22 | 32-Byte-Gerätekennung; gerätespezifisch | Einzelregister zusätzlich als Text dekodiert | EIGEN |
| 40047 | 2 Byte ASCII | R; Schreiben nicht getestet | Gerätekennung, Bytes 23–24 | 32-Byte-Gerätekennung; gerätespezifisch | Einzelregister zusätzlich als Text dekodiert | EIGEN |
| 40048 | 2 Byte ASCII | R; Schreiben nicht getestet | Gerätekennung, Bytes 25–26 | 32-Byte-Gerätekennung; gerätespezifisch | Einzelregister zusätzlich als Text dekodiert | EIGEN |
| 40049 | 2 Byte ASCII | R; Schreiben nicht getestet | Gerätekennung, Bytes 27–28 | 32-Byte-Gerätekennung; gerätespezifisch | Einzelregister zusätzlich als Text dekodiert | EIGEN |
| 40050 | 2 Byte ASCII | R; Schreiben nicht getestet | Gerätekennung, Bytes 29–30 | 32-Byte-Gerätekennung; gerätespezifisch | Einzelregister zusätzlich als Text dekodiert | EIGEN |
| 40051 | 2 Byte ASCII | R; Schreiben nicht getestet | Gerätekennung, Bytes 31–32 | 32-Byte-Gerätekennung; gerätespezifisch | Einzelregister zusätzlich als Text dekodiert | EIGEN |
| 40052 | 2 Byte ASCII | R; Schreiben nicht getestet | Versionsstring, Bytes 1–2 | Gesamtstring = `7.0.5.0/1.0.2.0` + NUL-Padding | Einzelregister zusätzlich als Text dekodiert | EIGEN |
| 40053 | 2 Byte ASCII | R; Schreiben nicht getestet | Versionsstring, Bytes 3–4 | Gesamtstring = `7.0.5.0/1.0.2.0` + NUL-Padding | Einzelregister zusätzlich als Text dekodiert | EIGEN |
| 40054 | 2 Byte ASCII | R; Schreiben nicht getestet | Versionsstring, Bytes 5–6 | Gesamtstring = `7.0.5.0/1.0.2.0` + NUL-Padding | Einzelregister zusätzlich als Text dekodiert | EIGEN |
| 40055 | 2 Byte ASCII | R; Schreiben nicht getestet | Versionsstring, Bytes 7–8 | Gesamtstring = `7.0.5.0/1.0.2.0` + NUL-Padding | Einzelregister zusätzlich als Text dekodiert | EIGEN |
| 40056 | 2 Byte ASCII | R; Schreiben nicht getestet | Versionsstring, Bytes 9–10 | Gesamtstring = `7.0.5.0/1.0.2.0` + NUL-Padding | Einzelregister zusätzlich als Text dekodiert | EIGEN |
| 40057 | 2 Byte ASCII | R; Schreiben nicht getestet | Versionsstring, Bytes 11–12 | Gesamtstring = `7.0.5.0/1.0.2.0` + NUL-Padding | Einzelregister zusätzlich als Text dekodiert | EIGEN |
| 40058 | 2 Byte ASCII | R; Schreiben nicht getestet | Versionsstring, Bytes 13–14 | Gesamtstring = `7.0.5.0/1.0.2.0` + NUL-Padding | Einzelregister zusätzlich als Text dekodiert | EIGEN |
| 40059 | 2 Byte ASCII | R; Schreiben nicht getestet | Versionsstring, Bytes 15–16 | Gesamtstring = `7.0.5.0/1.0.2.0` + NUL-Padding | Einzelregister zusätzlich als Text dekodiert | EIGEN |
| 40060 | 2 Byte ASCII | R; Schreiben nicht getestet | Versionsstring, Bytes 17–18 | Gesamtstring = `7.0.5.0/1.0.2.0` + NUL-Padding | Einzelregister zusätzlich als Text dekodiert | EIGEN |
| 40061 | 2 Byte ASCII | R; Schreiben nicht getestet | Versionsstring, Bytes 19–20 | Gesamtstring = `7.0.5.0/1.0.2.0` + NUL-Padding | Einzelregister zusätzlich als Text dekodiert | EIGEN |
| 40062 | 2 Byte ASCII | R; Schreiben nicht getestet | Versionsstring, Bytes 21–22 | Gesamtstring = `7.0.5.0/1.0.2.0` + NUL-Padding | Einzelregister zusätzlich als Text dekodiert | EIGEN |
| 40063 | 2 Byte ASCII | R; Schreiben nicht getestet | Versionsstring, Bytes 23–24 | Gesamtstring = `7.0.5.0/1.0.2.0` + NUL-Padding | Einzelregister zusätzlich als Text dekodiert | EIGEN |
| 40064 | 2 Byte ASCII | R; Schreiben nicht getestet | Versionsstring, Bytes 25–26 | Gesamtstring = `7.0.5.0/1.0.2.0` + NUL-Padding | Einzelregister zusätzlich als Text dekodiert | EIGEN |
| 40065 | 2 Byte ASCII | R; Schreiben nicht getestet | Versionsstring, Bytes 27–28 | Gesamtstring = `7.0.5.0/1.0.2.0` + NUL-Padding | Einzelregister zusätzlich als Text dekodiert | EIGEN |
| 40066 | 2 Byte ASCII | R; Schreiben nicht getestet | Versionsstring, Bytes 29–30 | Gesamtstring = `7.0.5.0/1.0.2.0` + NUL-Padding | Einzelregister zusätzlich als Text dekodiert | EIGEN |
| 40067 | 2 Byte ASCII | R; Schreiben nicht getestet | Versionsstring, Bytes 31–32 | Gesamtstring = `7.0.5.0/1.0.2.0` + NUL-Padding | Einzelregister zusätzlich als Text dekodiert | EIGEN |
| 40068 | UINT16 | R; Schreiben nicht getestet | Drehkodierschalter / I Max | Positionen: 0→32, 1→10, 2→13, 3→16, 4→20, 5→25, 6→32, A→65, B→66, C→67 | Alle zehn Stellungen einzeln geschaltet und Wert gelesen. 65/66/67 sind ASCII A/B/C. | EIGEN + HAGER |
| 40069 | UINT16 [A] | R; Schreiben nicht getestet | PP-Kabelnennstrom | kein Kabel→0; 13 A→13; 20 A→20; 32 A→32; 63 A→63 | PRO-TYP II PP-Schalter nacheinander auf N.C./13/20/32/63 A | EIGEN + GMC |
| 40070 | 2 Byte ASCII | R; Schreiben nicht getestet | Interner EVSE-/Ladezustandscode | A/N.C.→`F\0`; A/Kabel→`A1`; B/13 A→`A1`; C/13 A→`C2`; D/13 A→`D1`; E/13 A→`E\0` | PRO-TYP II CP A–E; bei B Verriegelung hörbar, bei C Leistungsschütze hörbar, bei E Schütze ab/Kabel entriegelt | EIGEN + GMC |
| 40071 | UINT16 [%] | R; Schreiben nicht getestet | CP PWM Duty Cycle | CP=C: PP13→21 %, PP20→33 %, PP32→53 %, PP63→53 %. Bei 40081=6 A →10 %, bei 32 A →53 %. | PP-Stufen mit CP=C sowie Schreibtest 40081 | EIGEN |
| 40072 | UINT16 Rohwert | R; Schreiben nicht getestet | **Hypothese:** Strom L1 | ohne Last 0; mit 1-phasigem Wasserkocher an L1 ca. 81–82, beim Ein/Aus Zwischenwerte um 44 | 2200-W-Wasserkocher an der L1-Steckdose des PRO-TYP II | EIGEN |
| 40073 | UINT16 Rohwert | RO im aktuellen Template; FC06+FC16 abgewiesen | **Hypothese:** Strom L2 | bisher 0 | Noch nicht mit echter L2-Last geprüft; Zuordnung nur aus Lage neben 40072/40074 | EIGEN – Hypothese |
| 40074 | UINT16 Rohwert | RO im aktuellen Template; FC06+FC16 abgewiesen | **Hypothese:** Strom L3 | bisher 0 | Noch nicht mit echter L3-Last geprüft; Zuordnung nur aus Lage neben 40072/40073 | EIGEN – Hypothese |
| 40075 | UINT16 | RO; FC06+FC16 abgewiesen | Unbekannt | in bisherigen Zuständen 65535 = 0xFFFF | Lesen in mehreren CP/PP-/Lastzuständen; Schreibtests FC06 und FC16 | EIGEN |
| 40076 | UINT16 | RO; FC06+FC16 abgewiesen | Unbekannt | in bisherigen Zuständen 65535 = 0xFFFF | Lesen in mehreren CP/PP-/Lastzuständen; Schreibtests FC06 und FC16 | EIGEN |
| 40077 | UINT16 | RO; FC06+FC16 abgewiesen | Unbekannt | in bisherigen Zuständen 65535 = 0xFFFF | Lesen in mehreren CP/PP-/Lastzuständen; Schreibtests FC06 und FC16 | EIGEN |
| 40078 | UINT16 | RO; FC06+FC16 abgewiesen | Unbekannt | in bisherigen Zuständen 65535 = 0xFFFF | Lesen in mehreren CP/PP-/Lastzuständen; Schreibtests FC06 und FC16 | EIGEN |
| 40079 | UINT16 | RO; FC06+FC16 abgewiesen | Unbekannt | in bisherigen Zuständen 65535 = 0xFFFF | Lesen in mehreren CP/PP-/Lastzuständen; Schreibtests FC06 und FC16 | EIGEN |
| 40080 | UINT16 | RO; FC06+FC16 abgewiesen | Unbekannt | in bisherigen Zuständen 65535 = 0xFFFF | Lesen in mehreren CP/PP-/Lastzuständen; Schreibtests FC06 und FC16 | EIGEN |
| 40081 | UINT16 [A] | **RW verifiziert**, FC06 und FC16 | Ladestrom-Sollwert / angebotener Maximalstrom | 6→40071=10 %; 32→40071=53 % | Bei CP=C und PP=32 A Register auf 6 bzw. 32 geschrieben; PWM folgte unmittelbar | EIGEN |
| 40082 | UINT16 | **RW verifiziert**, FC06 und FC16 | Unbekannt; vermutlich weiterer Grenz-/Konfigurationswert | Grundwert 32; 6 und 32 werden angenommen. Änderung 32→6 veränderte bei 40081=32 den CP-PWM-Wert nicht. | Gezielte Schreibtests mit 6/32 bei CP=C | EIGEN |
| 40083 | UINT16 / Enum | **RW verifiziert**, FC06 und FC16 | Steuerregister; **extern als Sonnenmodus beschrieben** | 1 und 2 bleiben stehen; 3 wird wieder auf 2 zurückgesetzt. Im Standalone-Test keine direkte Änderung von 40070/40071 beobachtet. | 1/2/3 geschrieben und zurückgelesen. Funktion 1/2 stammt aus EVCC-Wireshark-Fund. | EIGEN + EVCC |
| 40084 | UINT16 | RO; FC06+FC16 abgewiesen | Unbekannt | keine Bedeutung ermittelt | Lesen + Schreibtest FC06/FC16 | EIGEN |
| 40085 | UINT16 | RO; FC06+FC16 abgewiesen | Unbekannt | keine Bedeutung ermittelt | Lesen + Schreibtest FC06/FC16 | EIGEN |
| 40086 | 2 Byte ASCII | R; Schreiben bewusst nicht getestet | RFID-ID Zeichen 1–2 | `C0` bei Testkarte `C08D62C4` | RFID-Karte vor Leser gehalten | EIGEN |
| 40087 | 2 Byte ASCII | R; Schreiben bewusst nicht getestet | RFID-ID Zeichen 3–4 | `8D` bei Testkarte `C08D62C4` | RFID-Karte vor Leser gehalten | EIGEN |
| 40088 | 2 Byte ASCII | R; Schreiben bewusst nicht getestet | RFID-ID Zeichen 5–6 | `62` bei Testkarte `C08D62C4` | RFID-Karte vor Leser gehalten | EIGEN |
| 40089 | 2 Byte ASCII | R; Schreiben bewusst nicht getestet | RFID-ID Zeichen 7–8 | `C4` bei Testkarte `C08D62C4` | RFID-Karte vor Leser gehalten; Gesamt-ID entspricht exakt Kartenaufdruck | EIGEN |
| 40090 | UINT16 | RO; FC06+FC16 abgewiesen | Unbekannt | bisher keine belastbare Zuordnung | Lesen + Schreibtest FC06/FC16 mit vorhandenem Wert | EIGEN |
| 40091 | UINT16 | RO; FC06+FC16 abgewiesen | Unbekannt | bisher keine belastbare Zuordnung | Lesen + Schreibtest FC06/FC16 mit vorhandenem Wert | EIGEN |
| 40092 | UINT16 | RO; FC06+FC16 abgewiesen | Unbekannt | bisher keine belastbare Zuordnung | Lesen + Schreibtest FC06/FC16 mit vorhandenem Wert | EIGEN |
| 40093 | UINT16 | RO; FC06+FC16 abgewiesen | Unbekannt | bisher keine belastbare Zuordnung | Lesen + Schreibtest FC06/FC16 mit vorhandenem Wert | EIGEN |
| 40094 | UINT16 | RO; FC06+FC16 abgewiesen | Unbekannt | bisher keine belastbare Zuordnung | Lesen + Schreibtest FC06/FC16 mit vorhandenem Wert | EIGEN |
| 40095 | UINT16 | RO; FC06+FC16 abgewiesen | Unbekannt | bisher keine belastbare Zuordnung | Lesen + Schreibtest FC06/FC16 mit vorhandenem Wert | EIGEN |
| 40096 | UINT16 | RO; FC06+FC16 abgewiesen | Unbekannt | bisher keine belastbare Zuordnung | Lesen + Schreibtest FC06/FC16 mit vorhandenem Wert | EIGEN |
| 40097 | UINT16 | RO; FC06+FC16 abgewiesen | Unbekannt | bisher keine belastbare Zuordnung | Lesen + Schreibtest FC06/FC16 mit vorhandenem Wert | EIGEN |
| 40098 | UINT16 | RO; FC06+FC16 abgewiesen | Unbekannt | bisher keine belastbare Zuordnung | Lesen + Schreibtest FC06/FC16 mit vorhandenem Wert | EIGEN |
| 40099 | UINT16 | RO; FC06+FC16 abgewiesen | Unbekannt | bisher keine belastbare Zuordnung | Lesen + Schreibtest FC06/FC16 mit vorhandenem Wert | EIGEN |
| 40100 | UINT16 | RO; FC06+FC16 abgewiesen | Unbekannt | bisher keine belastbare Zuordnung | Lesen + Schreibtest FC06/FC16 mit vorhandenem Wert | EIGEN |
| 40101 | UINT16 | RO; FC06+FC16 abgewiesen | Unbekannt | bisher keine belastbare Zuordnung | Lesen + Schreibtest FC06/FC16 mit vorhandenem Wert | EIGEN |
| 40102 | UINT16 | R; FC06 abgewiesen; FC16 noch nicht separat bestätigt | **Hypothese:** verfügbare/aktive Phasenanzahl | im aktuellen Aufbau 3 | Wert 3 beobachtet; Schreibversuch auf 1 per FC06 → ILLEGAL_DATA_ADDRESS. Funktion muss noch durch echte 1P/3P-Umschaltung bestätigt werden. | EIGEN – Hypothese |

## Detailtests

### 40068 – Drehkodierschalter / I Max

Eigene Messung:

| physische Stellung | Beschriftung/Funktion | Register 40068 |
|---:|---|---:|
| 0 | Auto über EMC | 32 |
| 1 | 10 A | 10 |
| 2 | 13 A | 13 |
| 3 | 16 A | 16 |
| 4 | 20 A | 20 |
| 5 | 25 A | 25 |
| 6 | 32 A | 32 |
| 7 | A | 65 |
| 8 | B | 66 |
| 9 | C | 67 |

65/66/67 entsprechen ASCII **A/B/C**.

**Auffällig:** Stellung 0 (Auto/EMC) und die feste 32-A-Stellung liefern beide den Registerwert 32. Aus 40068 allein lässt sich daher nicht unterscheiden, ob der Drehschalter auf Auto oder fest 32 A steht.

Die Bedeutungen der Schalterstellungen stammen zusätzlich aus der Hager-Anleitung (**HAGER**).

### 40069 – PP-Kabelnennstrom

| PP-Simulation | 40069 |
|---|---:|
| kein Kabel | 0 |
| 13 A | 13 |
| 20 A | 20 |
| 32 A | 32 |
| 63 A | 63 |

Damit ist 40069 als erkannter PP-Kabelnennstrom in Ampere **verifiziert**.

### 40070 – interner Zustandsstring

40070 ist kein einfacher numerischer CP-Wert, sondern zwei ASCII-Bytes:

| CP | PP | Dezimal | Hex | Text | Beobachtung |
|---|---:|---:|---:|---|---|
| A | N.C. | 17920 | 0x4600 | F\0 | kein Kabel |
| A | 13/20/32/63 A | 16689 | 0x4131 | A1 | Kabel erkannt |
| B | 13 A | 16689 | 0x4131 | A1 | Verriegelung hörbar |
| C | 13 A | 17202 | 0x4332 | C2 | Leistungsschütze hörbar |
| D | 13 A | 17457 | 0x4431 | D1 | Belüftung gefordert |
| E | 13 A | 17664 | 0x4500 | E\0 | Schütze fallen ab, Kabel wird entriegelt |

Wichtig: **CP=B liefert weiterhin A1.** Das Register ist deshalb nicht einfach eine 1:1-Kopie des CP-Zustands, sondern ein interner Wallbox-Zustandscode.

### 40071 – CP-PWM Duty Cycle

Mit CP=C und wechselnder PP-Codierung:

| PP | 40071 |
|---:|---:|
| 13 A | 21 % |
| 20 A | 33 % |
| 32 A | 53 % |
| 63 A | 53 % |

Das entspricht im normalen IEC-61851-Bereich sehr genau **I ≈ DutyCycle × 0,6 A**:

- 21 % → ca. 12,6 A
- 33 % → ca. 19,8 A
- 53 % → ca. 31,8 A

Das 63-A-Kabel erhöht den PWM-Wert nicht weiter, weil die 22-kW-Wallbox auf 32 A begrenzt.

Zusätzlich bestätigt durch 40081:

- 40081 = 6 A → 40071 = 10 %
- 40081 = 32 A → 40071 = 53 %

### 40072–40074 – Phasenströme

40072 reagiert auf eine reale einphasige Last an L1:

- ohne Last: 0
- Wasserkocher an L1: ca. 81–82
- beim Ein-/Ausschalten Übergangswerte, u. a. ca. 44

Daher ist **40072 = Strom L1** sehr wahrscheinlich. Die Skalierung ist noch nicht abschließend bestätigt; **0,1 A pro Digit** ist eine plausible Arbeitshypothese, wird aber erst mit Referenzmessung festgeschrieben.

40073 und 40074 liegen direkt daneben und sind daher als **Strom L2 / Strom L3** vorgemerkt. Der PRO-TYP-II-Steckdosenabgriff liegt jedoch auf L1; eine Gegenprobe mit echter L2-/L3-Last steht noch aus.

### 40081 – Ladestrom-Sollwert

**RW verifiziert.**

Bei CP=C und PP=32 A:

| 40081 | 40071 |
|---:|---:|
| 6 A | 10 % |
| 32 A | 53 % |

Damit ist 40081 der direkte Ladestrom-Sollwert / angebotene Maximalstrom in Ampere.

Schreiben funktioniert mit **FC06 und FC16**.

### 40082 – unbekanntes RW-Register

- Grundwert im Test: 32
- Schreiben von 6 und 32 funktioniert mit FC06 und FC16
- Bei 40081=32 führte 40082=6 **nicht** zu einer Änderung des CP-PWM-Wertes (40071 blieb 53 %)

Daher noch **keine Benennung**. Eine Funktion als zweite Strom-/Sicherungs-/Konfigurationsgrenze ist denkbar, aber derzeit nicht bewiesen.

### 40083 – extern als Sonnenmodus beschrieben

Eigene Tests:

- 1 wird angenommen und bleibt stehen
- 2 wird angenommen und bleibt stehen
- 3 wird angenommen, springt danach wieder auf 2 zurück
- FC06 und FC16 funktionieren
- bei unseren Standalone-Tests war zwischen 1 und 2 keine unmittelbare Änderung an 40070/40071 sichtbar

Externe Quelle **EVCC**: In einem Wireshark-Mitschnitt wurde die direkte **Modbus-Adresse 82** der Wallbox beschrieben. Dort werden 1 und 2 dem Sonnenmodus zugeordnet. Da Modbus intern 0-basiert adressiert, entspricht Adresse 82 in unserer 40001-Darstellung **40083**.

Die externe Funktionsbeschreibung wird deshalb dokumentiert, aber weiterhin klar als **Fremdquelle** gekennzeichnet.

### 40086–40089 – RFID

Beim Vorhalten der Karte **C08D62C4**:

| Register | Dezimal | Hex | ASCII |
|---:|---:|---:|---|
| 40086 | 17200 | 0x4330 | C0 |
| 40087 | 14404 | 0x3844 | 8D |
| 40088 | 13874 | 0x3632 | 62 |
| 40089 | 17204 | 0x4334 | C4 |

Zusammengesetzt:

`C0` + `8D` + `62` + `C4` = **C08D62C4**

Damit ist der Aufbau als **8 ASCII-Zeichen über vier Register** eindeutig verifiziert.

Noch offen ist, wie die Autorisierung/Freigabe über Modbus erfolgt. Die RFID-Register wurden bewusst nicht beschrieben.

### 40102 – Phasenanzahl, Hypothese

- gelesener Wert: **3**
- FC06-Schreibversuch auf 1 → **ILLEGAL_DATA_ADDRESS**
- bisher keine echte 1P/3P-Umschaltung erzeugt

Die Hypothese lautet daher: **aktive oder verfügbare Phasenanzahl**. Bestätigt ist das erst, wenn bei einer realen Phasenumschaltung der Wert zwischen 1 und 3 wechselt.

## Schreibtests

### Sicher als RW bestätigt

| Register | FC06 | FC16 | Stand |
|---:|:---:|:---:|---|
| 40081 | ✅ | ✅ | Ladestrom-Sollwert |
| 40082 | ✅ | ✅ | Funktion unbekannt |
| 40083 | ✅ | ✅ | extern Sonnenmodus |

### Als nicht beschreibbar getestet

Für **40073–40080, 40084–40085 und 40090–40101** wurden FC06 und FC16 getestet. Die Wallbox verhielt sich bei beiden Schreibverfahren identisch und wies die Zugriffe ab.

Für **40002/40003** wurde FC06 abgewiesen; FC16 wurde dort noch nicht separat getestet.

Für **40102** wurde FC06 abgewiesen.

## Noch offene Punkte

1. Direkter Modbus-Befehl für **1P/3P-Phasenumschaltung**
2. Bestätigung, ob **40102** die aktive/verfügbare Phasenanzahl ist
3. Bedeutung von **40082**
4. Bedeutung von **40075–40080** (derzeit 0xFFFF)
5. Gegenprobe **40073/40074** mit Last auf L2/L3
6. Skalierung von **40072–40074**
7. Modbus-Mechanismus für **RFID-Autorisierung / Ladefreigabe**
8. Leistungs- und Energiezählerregister
9. Registerbereich **oberhalb 40102**
10. Untersuchung weiterer Modbus-Funktionsbereiche (z. B. Coils), falls Holding Register nicht alle Steuerbefehle abbilden


## Quellen und Herkunft

| ID | Quelle | Verwendung |
|---|---|---|
| **EIGEN** | Eigene Messungen an der Wallbox XEV1K22T2E3DC, dokumentiert am 01./02.10.2026 | Alle als **verifiziert** markierten Register, Schreibtests, CP/PP-Tests, Lasttest und RFID-Test |
| **HAGER** | [Hager Installationsanleitung witty solar XEV1K22T2S/XEV1K07T2S](https://assets.hager.com/step-content/P/HA_39675796/Document/std.lang.all/6LE009000B_XEV1KXX2S_EVCS_WITTY-SOLAR_MANUAL_DE_WEB.pdf) | Bedeutung des Drehkodierschalters: 0 = Auto über EMC, 10/13/16/20/25/32 A, A/B Teststellungen, C ohne Funktion |
| **GMC** | [Gossen Metrawatt PRO-TYP II – Support/Downloads](https://support.gossenmetrawatt.de/Main/DownloadCenter?id=Z525A) und [Produktseite](https://www.gossenmetrawatt.de/produkte/pro-typ-i-i/) | CP-Zustände A–E und PP-Kabelsimulation 13/20/32/63 A |
| **EVCC** | [evcc Discussion #14122 – Support für E3/DC Multi Connect II](https://github.com/evcc-io/evcc/discussions/14122) | Fremdfund: direkter Schreibzugriff auf Modbus-Adresse 82 der Wallbox; 1 = Sonnenmodus aus, 2 = Sonnenmodus ein |
| **PVF** | [Photovoltaikforum – Modbus Register E3DC Wallbox Multi Connect](https://www.photovoltaikforum.com/thread/198110-modbus-register-e3dc-wallbox-multi-connect/) | Früher externer Register-Dump. Nur als Vergleich; dessen damals beobachtete Grenze 40082 wurde durch unsere eigenen Tests bis 40102 erweitert. |

> **Wichtig:** Es gibt derzeit keine öffentlich bekannte offizielle Registerliste für den direkten Modbus-TCP-Zugriff auf die Multi Connect II. Deshalb wird in dieser Doku strikt zwischen **eigener Verifikation**, **Hypothese** und **Fremdquelle** unterschieden.

