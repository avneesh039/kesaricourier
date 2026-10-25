// ecosystem.config.js
// Production PM2 Process Manager Configuration for Courier ERP SaaS

module.exports = {
  apps: [
    {
      name: 'kesari-backend-api',
      cwd: './backend',
      script: 'dist/server.js',
      instances: 'max',
      exec_mode: 'cluster',
      autorestart: true,
      watch: false,
      max_memory_restart: '1G',
      env: {
        NODE_ENV: 'development',
        PORT: 5000,
      },
      env_production: {
        NODE_ENV: 'production',
        PORT: 5000,
      },
    },
  ],
};
