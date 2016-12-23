from django.conf.urls import include, url
from django.contrib import admin
from django.contrib.auth import views as auth_views
from WWW_Dashboard.views import *
from rest_framework import routers


router = routers.DefaultRouter()
router.register(r'users', UserViewSet)
router.register(r'groups', GroupViewSet)
router.register(r'data-gca', MasterDataViewSetGCA)
router.register(r'data-ngca', MasterDataViewSetNGCA)
# router.register(r'data-raw', MasterDataViewJSON)

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
    url(r'^admin/', admin.site.urls),
    url(r'^data/', users_json),
    # url(r'^accounts/', include('registration.backends.hmac.urls')),
    url(r'^test/', test),
    url(r'^data-raw/$', MasterDataViewJSON.as_view()),
    url(r'^', include(router.urls)),
    url(r'^api-auth/', include('rest_framework.urls', namespace='rest_framework'))

]
