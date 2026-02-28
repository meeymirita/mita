package models
// VerificationMessage — отправка кода подтверждения при регистрации (очередь emails_queue)

type VerificationMessage struct {
    Email   string `json:"email"`
    UserID  int    `json:"user_id"`
    Type    string `json:"type"`
}

// ResetPasswordMessage — сообщение для письма со ссылкой сброса пароля (очередь reset_password_queue)
type ResetPasswordMessage struct {
    Email   string `json:"email"`
    Token   string `json:"token"`
    UserID  int    `json:"user_id"`
    Subject string `json:"subject"`
    HTML    string `json:"html"`
}
