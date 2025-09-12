package app.security;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.context.annotation.Configuration;
import org.springframework.security.authentication.AuthenticationManager;
import org.springframework.security.config.annotation.authentication.builders.AuthenticationManagerBuilder;
import org.springframework.security.config.annotation.authentication.configuration.AuthenticationConfiguration;
import org.springframework.security.config.annotation.web.builders.HttpSecurity;
import org.springframework.security.config.annotation.web.configurers.AbstractHttpConfigurer;
import org.springframework.security.core.userdetails.User;
import org.springframework.security.core.userdetails.UserDetails;
import org.springframework.security.core.userdetails.UserDetailsService;
import org.springframework.security.crypto.bcrypt.BCryptPasswordEncoder;
import org.springframework.security.crypto.password.PasswordEncoder;
import org.springframework.security.provisioning.InMemoryUserDetailsManager;
import org.springframework.security.web.SecurityFilterChain;
import org.springframework.context.annotation.Bean;

//@EnableWebSecurity
@Configuration
public class SecurityConfig {

    private final UserDetailsService userDetailsService;

    @Autowired
    public SecurityConfig(UserDetailsService userDetailsService) {
        this.userDetailsService = userDetailsService;
    }


    @Bean
    public SecurityFilterChain securityFilterChain(HttpSecurity http) throws Exception {
        System.out.println("⚡ SecurityConfig is active! ⚡");  // Проверка, что конфиг загружается
        String pass_1 = passwordEncoder().encode("1230");
//        String pass_2 = passwordEncoder().encode("1230");
//        String pass_3 = passwordEncoder().encode("1230");
//        System.out.println("Pass_1 : " + pass_1 + " | ");
//        System.out.println(new BCryptPasswordEncoder().matches("1230",pass_1));
//
//        System.out.println("Pass_2 : " + pass_2 + " | ");
//        System.out.println(new BCryptPasswordEncoder().matches("1230",pass_2));
//
//        System.out.println("Pass_3 : " + pass_3 + " | ");
//        System.out.println(new BCryptPasswordEncoder().matches("1230",pass_3));

        http
                .csrf(AbstractHttpConfigurer::disable)  // Новый синтаксис для отключения CSRF
                .authorizeHttpRequests(auth -> auth
                        .requestMatchers("/public/**", "/login", "/logout-success").permitAll()
                        .anyRequest().authenticated()
                )
                .formLogin(form -> form
                        .loginPage("/login").
                        defaultSuccessUrl("/cabinet",true).
                        permitAll()
                )
                .logout(logout -> logout
                        .logoutUrl("/logout")
                        .logoutSuccessUrl("/login?logout").permitAll()
                )
        ;

        return http.build();
    }
    // Здесь добавляется метод настройки пользователей в памяти
//    @Bean
//    public UserDetailsService userDetailsService() {
//        System.out.println("BEAN UserDetailsService");
//        UserDetails user = User.builder()
//                .username("user")
//                .password(passwordEncoder().encode("user"))
//                .roles("USER")
//                .build();
//        UserDetails admin = User.withUsername("admin")
//                .password(passwordEncoder().encode("1230"))
//                .roles("ADMIN")
//                .build();
//        System.out.println("Password : " + admin.getPassword());
//
//        return new InMemoryUserDetailsManager(admin,user);
//    }
    @Bean
    public PasswordEncoder passwordEncoder() {
        return new BCryptPasswordEncoder();

    }

    @Bean
    public AuthenticationManager authenticationManager(AuthenticationConfiguration configuration) throws Exception {
        return configuration.getAuthenticationManager();
    }


}


