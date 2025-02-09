export interface User {
  id: string;
  email: string;
  nom: string;
  prenom: string;
  twoFactorEnabled: boolean;
}

export interface LoginCredentials {
  email: string;
  password: string;
}

export interface RegisterCredentials extends LoginCredentials {
  nom: string;
  prenom: string;
  confirmPassword: string;
}

export interface AuthResponse {
  succes: boolean;
  message: string;
  data: object;
  token: string;
  expiration: string;
  active: boolean;
  user: User;
  otpRequired?: boolean;
  tfaRequired?: boolean;
}