package models

type VerificationMessage struct {
    Email  string `json:"email"`
    Code   string `json:"code"`
    UserID int    `json:"user_id"`
}
