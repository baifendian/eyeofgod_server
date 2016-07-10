
from django.conf.urls import patterns, url

from eogserver import views

urlpatterns = patterns('',

    url(r'^sensor/postdata',views.sensor_postdata),

    url(r'^app/register$', views.app_register),
    url(r'^app/subscription$', views.app_subscription),

    url(r'^app/page/(?P<pagename>[a-zA-Z]{1,10}).html$', views.app_get_page),
    url(r'^app/state/(?P<pagename>[a-zA-Z]{1,10})$', views.app_get_state),
    url(r'^app/info/registered',views.app_get_registeredinfo)
)




