// backend/src/server.ts
import express from 'express';
import cors from 'cors';
import helmet from 'helmet';
import morgan from 'morgan';
import cookieParser from 'cookie-parser';
import dotenv from 'dotenv';

dotenv.config();

import logger from './config/logger';
import prisma from './config/database';
import apiRoutes from './routes';
import { apiRateLimiter } from './middlewares/rateLimiter';
import { errorHandler } from './middlewares/errorHandler';

const app = express();
const PORT = process.env.PORT || 5000;

// Security Middlewares
app.use(
  helmet({
    contentSecurityPolicy: false, // Allows SVG and embedded thermal images
  })
);

app.use(
  cors({
    origin: process.env.CORS_ORIGIN || 'http://localhost:5173',
    credentials: true,
    methods: ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],
    allowedHeaders: ['Content-Type', 'Authorization', 'x-tenant-id'],
  })
);

// Body Parsers & Cookies
app.use(express.json({ limit: '10mb' }));
app.use(express.urlencoded({ extended: true, limit: '10mb' }));
app.use(cookieParser(process.env.COOKIE_SECRET || 'kesari_secret'));

// Logging
const morganStream = {
  write: (message: string) => logger.info(message.trim()),
};
app.use(morgan('combined', { stream: morganStream }));

// Rate Limiter
app.use('/api', apiRateLimiter);

// Health Check Endpoints
app.get('/health', (req, res) => {
  res.json({
    status: 'healthy',
    timestamp: new Date().toISOString(),
    service: 'Courier ERP SaaS Platform API',
    version: '1.0.0',
  });
});

app.get('/api/health', (req, res) => {
  res.json({
    status: 'healthy',
    timestamp: new Date().toISOString(),
    service: 'Courier ERP SaaS Platform API',
  });
});

// Mount Master API Router
app.use('/api/v1', apiRoutes);

// Centralized Error Handler
app.use(errorHandler);

// Server Listen
const server = app.listen(PORT, () => {
  logger.info(`🚀 Courier ERP SaaS Backend running on http://localhost:${PORT}`);
  logger.info(`📦 MySQL Database connected via Prisma Client`);
  logger.info(`🔒 Environment: ${process.env.NODE_ENV || 'development'}`);
});

// Graceful Shutdown
const shutdown = async (signal: string) => {
  logger.info(`Received ${signal}. Shutting down gracefully...`);
  server.close(async () => {
    logger.info('HTTP server closed.');
    await prisma.$disconnect();
    logger.info('Database connection closed.');
    process.exit(0);
  });
};

process.on('SIGTERM', () => shutdown('SIGTERM'));
process.on('SIGINT', () => shutdown('SIGINT'));

export default app;
