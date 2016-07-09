from django.conf.urls import patterns, include, url

from django.contrib import admin
admin.autodiscover()

urlpatterns = patterns('',
    # all url connect will connect app eogserver
    url(r'^', include('eogserver.urls')),
)
