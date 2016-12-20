from django.shortcuts import render

#views.py
from WWW_Dashboard.forms import *
from django.contrib.auth.decorators import login_required, user_passes_test
from django.contrib.auth import logout
from django.views.decorators.csrf import csrf_protect
from django.shortcuts import render_to_response, render
from django.http import HttpResponseRedirect, HttpResponse
from django.template import RequestContext
import json
from django.http import HttpResponse
from django.core import serializers
import psycopg2

def ajax(request):
    data = {}
    data['something'] = 'useful'
    return HttpResponse(json.dumps(data), content_type = "application/json")


def users_json(request):

    # conn = psycopg2.connect(database='3W_DB', user='postgres', password='3w_reach')
    conn = psycopg2.connect(database='ebdb', user='postgres', password='django2016')
    cur = conn.cursor()
    cur.execute("""select row_to_json(t) from (select * from \"WWWData\" WHERE area_type = 'NGCA') t""")

    rows = cur.fetchall()
    # select row_to_json(t) from (select * from \"WWWData\") t
    print(rows)
    # data = serializers.serialize("json", rows)
    # print(data)
    return HttpResponse(json.dumps(rows), content_type='application/json')



@csrf_protect
def register(request):

    if request.method == 'POST':
        form = RegistrationForm(request.POST)
        if form.is_valid():
            user = User.objects.create_user(
                username=form.cleaned_data['username'],
                password=form.cleaned_data['password1'],
                email=form.cleaned_data['email']
            )
            return HttpResponseRedirect('/register/success/')

    else:
        form = RegistrationForm()

    # variables = RequestContext(request, {
    #             'form': form
    #         })

    return render(request, 'registration/register.html', {'form': form})


def register_success(request):
    return render_to_response(
        'registration/success.html',
    )

def logout_page(request):
    logout(request)
    return HttpResponseRedirect('/')

# def home(request):
#     return render_to_response(
#         'home.html',
#         { 'user': request.user }
#     )


def home(request):
    return render_to_response(
        'home.html',
        { 'user': request.user,
          'access': check_access(request.user)
        }
    )

def test(request):
    return render(
        request,
        'test.html',
        { 'user': request.user,
          'access': check_access(request.user)
        }
    )

def donbas(request):
    return render_to_response(
        'donbas.html',
        { 'user': request.user ,
          'access': check_access(request.user)
        }
    )

def check_access(user):
    if user:
        return user.groups.filter(name='Staff').count() > 0
    return False

@login_required
@user_passes_test(check_access, login_url='../login/')
def donbas_ngca(request):
    return render_to_response(
        'donbas_ngca.html',
        { 'user': request.user,
          'access': check_access(request.user)
        }
    )