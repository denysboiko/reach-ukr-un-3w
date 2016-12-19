"""
WSGI config for REACH_3W project.

It exposes the WSGI callable as a module-level variable named ``application``.

For more information on this file, see
https://docs.djangoproject.com/en/1.10/howto/deployment/wsgi/
"""

import os

from django.core.wsgi import get_wsgi_application

# os.environ.setdefault("DJANGO_SETTINGS_MODULE", "REACH_3W.settings")
os.environ['DJANGO_SETTINGS_MODULE'] = 'REACH_3W.settings'

application = get_wsgi_application()
