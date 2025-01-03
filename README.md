# OpenManage

**OpenManage** aims to provides tools for managing employees, contacts and absences, with more features planned in the future.

---

## Features

- **Person Management** 🟢 Developed: Handles common data of people a company interacts with, including customer contacts and employees. Basic functionality developed. More enhancements planned.
- **Employment Management** 🟡 Proposed: Related to persons with the `employee` type. To be developed, not yet brainstormed or planned out.
- **Absence Management** 🟢 Developed (Improvements Ongoing): Manages and tracks employee absences, allowing employees to request specific absence types.
- **Procedures** 🟡 Proposed: Implements procedures and routines within the company. Not yet planned out.

---

## Installation

1. Clone the repo.
2. `composer install`
3. `cp .env.example .env`
4. Set app key `php artisan key:generate`
5. Edit database settings in .env.
6. `php artisan migrate`
7. `php artisan db:seed`
8. Create your first user with `php artisan make:filament-user`
9. Follow the setup after installation.

---

## Setup

### Absence

1. Setup various absence types.
2. Set the default names for holidays and own sick leaves in Absence setting.

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
