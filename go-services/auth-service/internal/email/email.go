package email

import "net/smtp"

func SendEmail(to string, code string) error {
	from := "noreply@meeymirita.ru"
	host := "mailpit"
	port := "1025"

	msg := []byte("Subject: Code\r\n\r\nYour code: " + code)

	return smtp.SendMail(host+":"+port, nil, from, []string{to}, msg)
}
