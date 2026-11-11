import os
from pathlib import Path

# Directorio actual: src/main/PYTHON
PYTHON_DIR = Path(__file__).resolve().parent.parent
# Raíz del proyecto HandApp
PROJECT_ROOT = PYTHON_DIR.parent.parent.parent
# Recursos existentes de Spring Boot (templates y static originales para conservar el 100% de la estética)
RESOURCES_DIR = PYTHON_DIR.parent / 'resources'
TEMPLATES_DIR = RESOURCES_DIR / 'templates'
STATIC_DIR = RESOURCES_DIR / 'static'

SECRET_KEY = 'django-insecure-handapp-lsc-secret-key-2026'
DEBUG = True
ALLOWED_HOSTS = ['*']

INSTALLED_APPS = [
    'django.contrib.contenttypes',
    'django.contrib.sessions',
    'django.contrib.messages',
    'django.contrib.staticfiles',
]

MIDDLEWARE = [
    'django.middleware.security.SecurityMiddleware',
    'django.contrib.sessions.middleware.SessionMiddleware',
    'django.middleware.common.CommonMiddleware',
    'django.contrib.messages.middleware.MessageMiddleware',
]

ROOT_URLCONF = 'handapp_django.urls'

TEMPLATES = [
    {
        'BACKEND': 'django.template.backends.django.DjangoTemplates',
        'DIRS': [str(TEMPLATES_DIR)],
        'APP_DIRS': True,
        'OPTIONS': {
            'context_processors': [
                'django.template.context_processors.debug',
                'django.template.context_processors.request',
                'django.contrib.messages.context_processors.messages',
            ],
        },
    },
]

# Toda la persistencia de usuarios, lecciones y progreso está en MySQL (127.0.0.1:3306/handapp).
# Usamos sesiones firmadas en cookie para no depender de ningún archivo SQLite local.
DATABASES = {}
SESSION_ENGINE = 'django.contrib.sessions.backends.signed_cookies'
SESSION_COOKIE_HTTPONLY = True

LANGUAGE_CODE = 'es-co'
TIME_ZONE = 'America/Bogota'
USE_I18N = True
USE_TZ = True

STATIC_URL = '/static/'
STATICFILES_DIRS = [str(STATIC_DIR)]
DEFAULT_AUTO_FIELD = 'django.db.models.BigAutoField'
