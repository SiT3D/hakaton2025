package app.security;

import org.springframework.stereotype.Controller;
import org.springframework.web.bind.annotation.GetMapping;

@Controller
public class AuthController {

    @GetMapping("/login")
    public String login() {
        return "login"; // Загружает login.html
    }
    @GetMapping("/logout-success")
    public String logoutSuccess() {
        return "logout-success";  // Отобразит logout-success.html
    }
}