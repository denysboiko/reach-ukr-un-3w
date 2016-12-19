# -*- coding: utf-8 -*-
# Generated by Django 1.10.3 on 2016-12-01 13:06
from __future__ import unicode_literals

from django.db import migrations, models


class Migration(migrations.Migration):

    initial = True

    dependencies = [
    ]

    operations = [
        migrations.CreateModel(
            name='Image',
            fields=[
                ('id', models.AutoField(auto_created=True, primary_key=True, serialize=False, verbose_name='ID')),
                ('artist', models.CharField(max_length=200)),
                ('album', models.CharField(max_length=200)),
                ('year', models.CharField(max_length=4)),
                ('image_url', models.URLField()),
                ('load_date', models.DateTimeField(verbose_name='Loaded')),
                ('user', models.CharField(max_length=200)),
            ],
        ),
    ]