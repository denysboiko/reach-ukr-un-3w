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
        # fields = '__all__'
        fields = (
              'date'
            , 'cluster_name'
            , 'org_name'
            , 'partner1_name'
            , 'partner2_name'
            , 'admin1_id'
            , 'admin1_name_eng'
            , 'admin2_id'
            , 'admin2_name_ukr'
            , 'area_type'
            , 'status_name'
            , 'activity'
            , 'activity_start'
            , 'activity_end'
            , 'number_reached'
        )


class MasterDataSerializerRaw(serializers.Serializer):
    class Meta:
        model = Wwwdata
        # fields = '__all__'
        fields = (
              'date'
            , 'cluster_name'
            , 'partner1_name'
            , 'partner2_name'
            , 'admin1_id'
            , 'admin1_name_eng'
            , 'admin2_id'
            , 'admin2_name_ukr'
            , 'area_type'
            , 'status_name'
            , 'activity'
            , 'activity_start'
            , 'activity_end'
            , 'number_reached'
        )
