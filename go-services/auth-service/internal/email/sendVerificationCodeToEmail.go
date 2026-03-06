package email

import (
	"bytes"
	"encoding/base64"
	"fmt"
	"log"
	"net/smtp"
	"os"
	"path/filepath"
	"strings"
	"text/template"
)

func SendVerificationCodeToEmail(email string, code string) {
	fmt.Println("Sending email to", email)
	from := "noreply@meeymirita.ru"
	host := "mailpit"
	port := "1025"
	addr := host + ":" + port

	subjectEnc := "=?UTF-8?B?" + base64.StdEncoding.EncodeToString([]byte("Code")) + "?="
	contentType := "text/html; charset=UTF-8"

	body := []byte(buildHTML(email, code))

	buf := bytes.NewBuffer(nil)
	buf.WriteString("From: " + from + "\r\n")
	buf.WriteString("To: " + email + "\r\n")
	buf.WriteString("Subject: " + subjectEnc + "\r\n")
	buf.WriteString("MIME-Version: 1.0\r\n")
	buf.WriteString(fmt.Sprintf("Content-Type: %s\r\n", contentType))
	buf.WriteString("Content-Transfer-Encoding: base64\r\n")
	buf.WriteString("\r\n")
	buf.WriteString(base64.StdEncoding.EncodeToString(body))

	err := smtp.SendMail(addr, nil, from, []string{email}, buf.Bytes())
	if err != nil {
		log.Fatal("SendMail:", err)
	}
	fmt.Println("Email sent to", email)
}
func buildHTML(email string, code string) string {
	// Данные для подстановки в HTML шаблон
	EmailDataMap := map[string]string{
		"frontend_url": "https://meeymirita.ru/",
		"sakura_url": "http://localhost:8080/storage/images/image_to_email/sakura.png",
		"himary_url": "http://localhost:8080/storage/images/image_to_email/himary.jpg",
		"code": code,
	}

	// Получаем текущую рабочую директорию (в Docker это /app)
	wd, err := os.Getwd()
	if err != nil {
		log.Fatal(err)
	}
	// Шаблон лежит в internal/templates/verification.gohtml относительно корня проекта (/app)
	htmlPath := filepath.Join(wd, "internal", "templates", "verification.gohtml")
	htmlPath = filepath.Clean(htmlPath) 


	tmpl, err := template.ParseFiles(htmlPath)
	if err != nil {
		log.Fatal("Error parsing template:", err)
	}


	var buf strings.Builder
	err = tmpl.Execute(&buf, EmailDataMap)
	if err != nil {
		log.Fatal("Error executing template:", err)
	}
	htmlContent := buf.String()

	return htmlContent 
}