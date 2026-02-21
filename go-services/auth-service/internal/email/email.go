package email

import (
	"bytes"
	"encoding/base64"
	"fmt"
	"net/smtp"
)

func SendEmail(to string, code string, subject string, html string) error {
	from := "noreply@meeymirita.ru"
	host := "mailpit"
	port := "1025"
	addr := host + ":" + port

	if subject == "" {
		subject = "Код подтверждения"
	}
	subjectEnc := "=?UTF-8?B?" + base64.StdEncoding.EncodeToString([]byte(subject)) + "?="

	var body []byte
	contentType := "text/plain; charset=UTF-8"
	if html != "" {
		contentType = "text/html; charset=UTF-8"
		body = []byte(html)
	} else {
		body = []byte("Your code: " + code)
	}

	buf := bytes.NewBuffer(nil)
	buf.WriteString("From: " + from + "\r\n")
	buf.WriteString("To: " + to + "\r\n")
	buf.WriteString("Subject: " + subjectEnc + "\r\n")
	buf.WriteString("MIME-Version: 1.0\r\n")
	buf.WriteString(fmt.Sprintf("Content-Type: %s\r\n", contentType))
	buf.WriteString("Content-Transfer-Encoding: base64\r\n")
	buf.WriteString("\r\n")
	buf.WriteString(base64.StdEncoding.EncodeToString(body))

	return smtp.SendMail(addr, nil, from, []string{to}, buf.Bytes())
}
