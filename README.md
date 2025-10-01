# Shopware Symfony App

Dieses Projekt ist eine Symfony-Anwendung zur Integration mit Shopware über die Shopware App Bundle-Schnittstelle. Es ermöglicht das Abrufen und Speichern von Bestelldaten aus Shopware in einer lokalen Datenbank.

## Features
- Abruf von Bestelldaten aus Shopware per Order-ID
- Speicherung von Order-ID, Order-Nummer und Gesamtwert in einer Datenbanktabelle
- Token-Generierung für die Shopware-API über Client-ID und Client-Secret
- Web-Formular zur manuellen Eingabe einer Order-ID und Anzeige der Bestelldaten
- Fehler- und Status-Logging für API-Requests

## Installation
1. **Abhängigkeiten installieren**
   ```bash
   composer install
   ```
2. **Umgebungsvariablen konfigurieren**
   Passe die Werte für Datenbank und Shopware-API in `.env` oder `.env.local` an.
3. **Datenbankmigration ausführen**
   ```bash
   php bin/console doctrine:migrations:migrate
   ```
4. **Symfony-Server starten**
   ```bash
   symfony server:start
   ```

## Nutzung
- Rufe im Browser `/order/form` auf, um das Bestellformular zu nutzen.
- Gib eine gültige Shopware-Order-ID ein und erhalte die Bestelldaten.

## Konfiguration
- Die Shop-Daten (Client-ID, Client-Secret, Shop-URL) werden in der Shop-Entity gepflegt.
- Die Shopware-API muss für die Token-Generierung und den Bestellabruf freigeschaltet sein.

## Troubleshooting
- **401 Unauthorized:** Prüfe Client-ID und Client-Secret sowie die Freischaltung der Shopware-App.
- **PROTOCOL_ERROR:** Erzwinge HTTP/1.1 im Token-Request und nutze `application/x-www-form-urlencoded` als Content-Type.
- **Formularfehler:** Stelle sicher, dass das Symfony FormBundle installiert und aktiviert ist.

## Weiterentwicklung
- Erweiterung um weitere Shopware-API-Endpunkte
- Automatisierte Verarbeitung von Bestell-Hooks
- Erweiterte Fehlerbehandlung und Monitoring

## Lizenz
Dieses Projekt steht unter der MIT-Lizenz.

