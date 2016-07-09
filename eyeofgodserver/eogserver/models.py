# This is an auto-generated Django model module.
# You'll have to do the following manually to clean this up:
#   * Rearrange models' order
#   * Make sure each model has one field with primary_key=True
#   * Remove `managed = False` lines for those models you wish to give write DB access
# Feel free to rename the models, but don't rename db_table values or field names.
#
# Also note: You'll have to insert the output of 'django-admin.py sqlcustom [appname]'
# into your database.
from __future__ import unicode_literals

from django.db import models

class Device(models.Model):
    id = models.IntegerField(primary_key=True)
    mark = models.CharField(max_length=128, blank=True)
    name = models.CharField(max_length=64, blank=True)
    model = models.CharField(max_length=64, blank=True)
    brand = models.CharField(max_length=20, blank=True)
    createtime = models.IntegerField(blank=True, null=True)
    class Meta:
        managed = False
        db_table = 'device'

class Dict(models.Model):
    textid = models.IntegerField(primary_key=True)
    text = models.CharField(max_length=20, blank=True)
    class Meta:
        managed = False
        db_table = 'dict'

class Event(models.Model):
    id = models.BigIntegerField(primary_key=True)
    mark = models.CharField(max_length=128, blank=True)
    state = models.IntegerField(blank=True, null=True)
    info = models.CharField(max_length=512, blank=True)
    timestamp = models.IntegerField(blank=True, null=True)
    class Meta:
        managed = False
        db_table = 'event'

class MonitorApp(models.Model):
    uid = models.IntegerField(blank=True, null=True)
    createtime = models.IntegerField(blank=True, null=True)
    sourceid = models.IntegerField(blank=True, null=True)
    class Meta:
        managed = False
        db_table = 'monitor_app'

class MonitorDevice(models.Model):
    deviceid = models.IntegerField()
    state = models.IntegerField(blank=True, null=True)
    class Meta:
        managed = False
        db_table = 'monitor_device'

class Remind(models.Model):
    id = models.IntegerField(primary_key=True)
    userid = models.IntegerField(blank=True, null=True)
    sourceid = models.IntegerField(blank=True, null=True)
    state = models.IntegerField(blank=True, null=True)
    createtime = models.IntegerField(blank=True, null=True)
    class Meta:
        managed = False
        db_table = 'remind'

class Source(models.Model):
    id = models.IntegerField(primary_key=True)
    textid = models.IntegerField(blank=True, null=True)
    location = models.CharField(max_length=1, blank=True)
    mark = models.CharField(max_length=128, blank=True)
    state = models.IntegerField(blank=True, null=True)
    class Meta:
        managed = False
        db_table = 'source'

class User(models.Model):
    id = models.IntegerField(primary_key=True)
    sex = models.IntegerField()
    location = models.CharField(max_length=1, blank=True)
    advanced = models.IntegerField(blank=True, null=True)
    mac = models.CharField(max_length=64, blank=True)
    createtime = models.IntegerField(blank=True, null=True)
    modifytime = models.IntegerField(blank=True, null=True)
    token = models.CharField(max_length=64, blank=True)
    class Meta:
        managed = False
        db_table = 'user'

