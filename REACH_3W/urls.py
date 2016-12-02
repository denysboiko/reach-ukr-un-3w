from django.conf.urls import url
from django.contrib import admin
from django.contrib.auth import views as auth_views

from WWW_Dashboard.views import *


urlpatterns = [

    url(r'^$', home),
    url(r'^logout/$', logout_page),
    url(r'^login/$', auth_views.login),
    url(r'^accounts/login/$', auth_views.login),
    url(r'^accounts/login/$', auth_views.login),
    url(r'^register/$', register),
    url(r'^register/success/$', register_success),
    url(r'^home/$', home),
    url(r'^donbas/$', donbas),
    url(r'^donbas_ngca/$', donbas_ngca),
    # url(r'^ngca/$', ngca),
    url(r'^admin/', admin.site.urls),
    url(r'^data/', users_json)
]