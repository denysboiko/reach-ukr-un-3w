from __future__ import unicode_literals
from django.db import models

class Wwwdata(models.Model):
    date = models.DateField(blank=True, null=True)
    cluster_name = models.CharField(max_length=80, blank=True, null=True)
    org_name = models.CharField(max_length=80, blank=True, null=True)
    partner1_name = models.CharField(max_length=80, blank=True, null=True)
    partner2_name = models.CharField(max_length=80, blank=True, null=True)
    admin1_id = models.BigIntegerField(blank=True, null=True)
    admin1_name_eng = models.CharField(max_length=80, blank=True, null=True)
    admin2_id = models.BigIntegerField(blank=True, null=True)
    admin2_name_ukr = models.CharField(max_length=80, blank=True, null=True)
    area_type = models.CharField(max_length=80, blank=True, null=True)
    status_name = models.CharField(max_length=80, blank=True, null=True)
    activity = models.CharField(max_length=350, blank=True, null=True)
    activity_start = models.DateField(blank=True, null=True)
    activity_end = models.DateField(blank=True, null=True)
    number_reached = models.FloatField(blank=True, null=True)
    comments = models.CharField(max_length=255, blank=True, null=True)

    class Meta:
        managed = False
        db_table = 'WWWData'