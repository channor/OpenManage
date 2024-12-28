# OpenManage

**OpenManage** is an open-source management application for companies, quickly developed with [Laravel](https://laravel.com/) and [Filament](https://filamentphp.com/). It provides tools for managing employees, contacts and absences, with more features planned in the future.

---

## Features

- **Employee Management**: Create and manage employee profiles as a part of Person model.
- **Absence Tracking**: Record and track vacations, sick leaves, and other absences.
- **People Management**: Centralize and maintain company contacts.

---

## PLANNED / TODO

- [ ] Add inactivity at date column and logic to users.
- [ ] Absence tracking/management.
- [ ] Welcome email new users.
- [ ] Create new user option with roles selection when creating person.
- [ ] Add logic for absences where type `has_hours = true`.

---

## Installation

1. Clone the repo.
2. `composer install`
3. `cp .env.example .env`
4. Set app key `php artisan key:generate`
5. Edit database settings in .env.
6. `php artisan migrate`
7. Create your first user with `php artisan make:filament-user`

---

## Contributing

OpenManage is developed by [@channor](https://github.com/channor) primarily for personal/company needs, but we welcome any and all contributions:

1. **Fork the repository**  
2. **Create a feature branch** (`git checkout -b feature/my-new-feature`)  
3. **Commit your changes** (`git commit -m 'Add some feature'`)  
4. **Push to the branch** (`git push origin feature/my-new-feature`)  
5. **Open a Pull Request** on GitHub

Feel free to open issues for bug reports, feature suggestions, or general discussions.

---

## License

OpenManage is open-source software licensed under the [MIT license](/LICENSE).  
Please see the [LICENSE](/LICENSE) file for more information.
