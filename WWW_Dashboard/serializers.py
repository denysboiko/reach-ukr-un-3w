from WWW_Dashboard.models import Wwwdata
from django.contrib.auth.models import User, Group
from rest_framework import serializers


class UserSerializer(serializers.HyperlinkedModelSerializer):
    class Meta:
        model = User
        fields = ('url', 'username', 'email', 'groups')

class GroupSerializer(serializers.HyperlinkedModelSerializer):
    class Meta:
        model = Group
        fields = ('url', 'name')

class MasterDataSerializer(serializers.HyperlinkedModelSerializer):
    class Meta:
        model = Wwwdata
        fields = ('url', 'cluster_name', 'org_name')