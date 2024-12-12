# Server Management Portal

A simple, secure portal to manage your server information, credentials, and databases. Built with Laravel and Filament.

## About

Keep track of all your servers, credentials, and databases in one secure place. No more spreadsheets or unsecured password lists - just a clean, organized way to store your infrastructure information.

## Features

### 🖥️ Server Management
- Store server details (hostname, IPs, SSH ports)
- Track hardware specs (CPU, RAM, Storage)
- Manage virtual servers with parent-child relationships
- One-click SSH connection

### 🔐 Credential Management
- Securely store encrypted passwords
- Manage SSH keys
- Support for various credential types (SSH, database, FTP)
- Easy password reveal and copy

### 🏢 Infrastructure
- Organize by datacenter
- Network management
- Simple database instance tracking

## Security

- All credentials encrypted at rest
- Secure password handling
- Role-based access control

## Tech Stack

- Laravel 10
- Filament 3
- PHP 8.1+
- MySQL/PostgreSQL

## Roadmap

- [ ] Server templates
- [ ] Batch operations
- [ ] API access
- [ ] Server groups
- [ ] Custom fields
- [ ] Export/Import functionality

## Contributing

Contributions are welcome! Feel free to submit pull requests or open issues for any bugs or feature requests.

## Credits

Built with ❤️ using:
- [Laravel](https://laravel.com)
- [Filament](https://filamentphp.com)

## License

This project is open-sourced software licensed under the [MIT license](LICENSE.md).