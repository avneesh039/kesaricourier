// backend/src/routes/authRoutes.ts
import { Router } from 'express';
import {
  login,
  refreshToken,
  logout,
  getMe,
  registerAgency,
} from '../controllers/authController';
import { authenticate } from '../middlewares/auth';
import { authRateLimiter } from '../middlewares/rateLimiter';

const router = Router();

router.post('/login', authRateLimiter, login);
router.post('/refresh-token', refreshToken);
router.post('/logout', logout);
router.get('/me', authenticate, getMe);
router.post('/register-agency', authRateLimiter, registerAgency);

export default router;
