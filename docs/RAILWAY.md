# Deploy to Railway

This guide will walk you through deploying this Laravel application to Railway using the one-click deployment solution.

## About This Application

This is a comprehensive **public safety, military and organizational management system** built with Laravel. It provides a complete solution for managing personnel, records, operations, and communications within structured organizations.

### Key Benefits

- **Complete Personnel Management** - Track users, ranks, positions, assignments, and organizational structure
- **Comprehensive Records System** - Manage awards, combat records, service history, training, and qualifications
- **Robust Communication Tools** - Internal messaging, Discord integration, Twilio SMS, and mass notifications
- **Event & Schedule Management** - Calendar system with recurring events and attendance tracking
- **API-First Design** - RESTful API with JWT authentication for third-party integrations
- **Role-Based Permissions** - Granular access control with customizable roles and permissions
- **Custom Field System** - Flexible data structures to meet your organization's unique needs

### Core Features

#### Organizational Management
- Hierarchical rank structure and specialty classifications
- Unit and position assignment tracking
- User approval workflows and online status monitoring
- Administrative dashboard with analytics

#### Records & Documentation
- Assignment records with service history
- Award and commendation tracking
- Combat and qualification records
- Training and competency management
- Document management with categories and tags
- Custom field data for extended attributes

#### Communication & Notifications
- Internal messaging system
- Real-time notifications
- Discord webhook integration
- Twilio SMS notifications
- Mass email capabilities
- Activity logging and audit trails

#### Operations & Scheduling
- Event calendar with registration
- Recurring schedules using RRule
- Attendance tracking
- Form submissions and newsfeeds

#### Developer Features
- RESTful API with automatic CRUD endpoints
- JWT authentication
- API versioning and rate limiting
- Comprehensive logging
- Queue-based background processing with Horizon

## One-Click Railway Deployment

### Prerequisites

Before deploying, ensure you have:
- A Railway account (sign up at [railway.app](https://railway.app))
- A GitHub account (if deploying from a repository)
- Basic understanding of environment variables

### Deploy Now

Click the button below to deploy to Railway:

[![Deploy on Railway](https://railway.com/button.svg)](https://railway.com/deploy/perscom?referralCode=O-oe8s&utm_medium=integration&utm_source=template&utm_campaign=generic)

### Deployment Steps

1. **Click the Deploy Button** above or create a new project in Railway
2. **Connect Your Repository** - Authorize Railway to access your GitHub repository
3. **Configure Services** - Railway will automatically detect and set up:
   - Laravel Application
   - Queue and Scheduled Tasks Worker
   - MySQL Database
   - Redis Cache

4. **Deploy** - Railway will automatically build and deploy your application and set all the necessary environment variables.

### Support & Resources

- **Documentation**: Check the `/docs` directory for detailed guides
- **Development Commands**: See `CLAUDE.md` for testing and development workflows
- **API Documentation**: Access `/api/documentation` after deployment
- **Laravel Documentation**: [laravel.com/docs](https://laravel.com/docs)
- **Railway Documentation**: [docs.railway.app](https://docs.railway.app)

### Security Best Practices

1. **Never commit** `.env` files to version control
2. **Rotate secrets** regularly (APP_KEY, JWT_SECRET, database passwords)
3. **Enable HTTPS** for all production deployments (Railway provides this automatically)
4. **Use strong passwords** for database and admin accounts
5. **Enable rate limiting** for API endpoints (configured by default)
6. **Monitor logs** for suspicious activity
7. **Keep dependencies updated** regularly:
   ```bash
   composer update
   npm update
   ```
8. **Enable two-factor authentication** for admin accounts
9. **Regular backups** - Configure Railway's backup solution or use external backup services
10. **Security headers** - Ensure proper CSP, HSTS, and other security headers are configured

### Cost Optimization

Railway pricing tips:

- **Start with Hobby plan** for small organizations
- **Monitor resource usage** in Railway dashboard
- **Optimize queries** to reduce database load
- **Use Redis caching** effectively to reduce compute time
- **Enable horizontal scaling** only when needed
- **Clean up old logs and data** regularly

---

**Ready to deploy?** Click the Deploy to Railway button above and get your organization management system running in minutes!
