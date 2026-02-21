#!/bin/sh
cd /app && go build -o /app/tmp/main ./cmd/send-code
cd /app && go build -o /app/tmp/main ./cmd/send-reset-password-link