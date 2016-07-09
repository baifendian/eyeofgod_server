
from django.conf.urls import patterns, url

from eogserver import views

urlpatterns = patterns('',
    url(r'^test/$', views.test ),

    url(r'^app/register$', views.app_register),
    url(r'^app/getregisterinfo$', views.app_getregisterinfo),
    url(r'^app/getstates$', views.app_getstates),
    url(r'^app/subscription$', views.app_subscription),
    url(r'^app/page/online$', views.app_page_online),
)




