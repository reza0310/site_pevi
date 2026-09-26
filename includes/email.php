<?php

class SmtpMailer
{
    private string $host;
    private int $port;
    private string $username;
    private string $password;
    private int $timeout;

    /**
     * @param string $host     Hôte du serveur SMTP (ex: mail.example.com)
     * @param int    $port     Port pour TLS implicite (465 par défaut)
     * @param string $username Identifiant SMTP
     * @param string $password Mot de passe SMTP
     * @param int    $timeout  Timeout de connexion en secondes
     */
    public function __construct(
        string $host,
        int $port = 465,
        string $username = '',
        string $password = '',
        int $timeout = 10
    ) {
        $this->host = $host;
        $this->port = $port;
        $this->username = $username;
        $this->password = $password;
        $this->timeout = $timeout;
    }

    /**
     * Envoie un e-mail au format texte brut et/ou HTML.
     *
     * @param string      $fromEmail Adresse de l'expéditeur
     * @param string      $fromName  Nom d'affichage de l'expéditeur
     * @param string      $toEmail   Adresse du destinataire
     * @param string      $subject   Sujet de l'e-mail
     * @param string      $textBody  Contenu en texte brut
     * @param string|null $htmlBody  Contenu HTML (optionnel)
     * @return bool
     * @throws Exception
     */
    public function send(
        string $fromEmail,
        string $fromName,
        string $toEmail,
        string $subject,
        string $textBody,
        ?string $htmlBody = null
    ): bool {
        // En TLS implicite, la connexion doit ouvrir un flux SSL dès le départ
        $remoteSocket = "ssl://{$this->host}:{$this->port}";

        $socket = @stream_socket_client(
            $remoteSocket,
            $errno,
            $errstr,
            $this->timeout,
            STREAM_CLIENT_CONNECT
        );

        if (!$socket) {
            throw new Exception("Erreur de connexion SMTP ({$errno}) : {$errstr}");
        }

        try {
            $this->readResponse($socket, 220);

            // Dialogue HELO/EHLO
            $this->sendCommand($socket, "EHLO " . gethostname(), 250);

            // Authentification SMTP (AUTH LOGIN)
            if (!empty($this->username) && !empty($this->password)) {
                $this->sendCommand($socket, "AUTH LOGIN", 334);
                $this->sendCommand($socket, base64_encode($this->username), 334);
                $this->sendCommand($socket, base64_encode($this->password), 235);
            }

            // Enveloppe SMTP
            $this->sendCommand($socket, "MAIL FROM: <{$fromEmail}>", 250);
            $this->sendCommand($socket, "RCPT TO: <{$toEmail}>", 250);

            // Envoi des données
            $this->sendCommand($socket, "DATA", 354);

            $headersAndBody = $this->buildPayload(
                $fromEmail,
                $fromName,
                $toEmail,
                $subject,
                $textBody,
                $htmlBody
            );

            // Envoi du message suivi de la séquence de fin de données (CRLF.CRLF)
            $this->sendCommand($socket, $headersAndBody . "\r\n.", 250);

            // Fermeture propre
            $this->sendCommand($socket, "QUIT", 221);
            fclose($socket);

            return true;
        } catch (Exception $e) {
            if (is_resource($socket)) {
                fclose($socket);
            }
            throw $e;
        }
    }

    /**
     * Construit les en-têtes et le corps MIME (Multipart/Alternative).
     */
    private function buildPayload(
        string $fromEmail,
        string $fromName,
        string $toEmail,
        string $subject,
        string $textBody,
        ?string $htmlBody
    ): string {
        $boundary = "==MULTIPART_BOUNDARY_" . md5(uniqid((string)microtime(true), true));

        $headers = [];
        $headers[] = "From: =?UTF-8?B?" . base64_encode($fromName) . "?= <{$fromEmail}>";
        $headers[] = "To: <{$toEmail}>";
        $headers[] = "Subject: =?UTF-8?B?" . base64_encode($subject) . "?=";
        $headers[] = "MIME-Version: 1.0";
        $headers[] = "Date: " . date(DATE_RFC2822);
        $headers[] = "Content-Type: multipart/alternative; boundary=\"{$boundary}\"";

        $body = [];

        // Version Texte Brut
        $body[] = "--{$boundary}";
        $body[] = "Content-Type: text/plain; charset=UTF-8";
        $body[] = "Content-Transfer-Encoding: base64";
        $body[] = "";
        $body[] = chunk_split(base64_encode($textBody));

        // Version HTML (si présente)
        if (!empty($htmlBody)) {
            $body[] = "--{$boundary}";
            $body[] = "Content-Type: text/html; charset=UTF-8";
            $body[] = "Content-Transfer-Encoding: base64";
            $body[] = "";
            $body[] = chunk_split(base64_encode($htmlBody));
        }

        $body[] = "--{$boundary}--";

        return implode("\r\n", $headers) . "\r\n\r\n" . implode("\r\n", $body);
    }

    /**
     * Envoie une commande au serveur SMTP et vérifie le code de réponse.
     */
    private function sendCommand($socket, string $command, int $expectedCode): string
    {
        fwrite($socket, $command . "\r\n");
        return $this->readResponse($socket, $expectedCode);
    }

    /**
     * Lit la réponse du serveur SMTP.
     */
    private function readResponse($socket, int $expectedCode): string
    {
        $response = '';
        while ($line = fgets($socket, 512)) {
            $response .= $line;
            // Si le 4ème caractère est un espace, c'est la fin de la réponse (ex: "250 OK")
            if (isset($line[3]) && $line[3] === ' ') {
                break;
            }
        }

        $statusCode = (int)substr($response, 0, 3);
        if ($statusCode !== $expectedCode) {
            throw new Exception("Erreur SMTP [Code {$statusCode}] attendu [{$expectedCode}] : {$response}");
        }

        return $response;
    }
}

?>
