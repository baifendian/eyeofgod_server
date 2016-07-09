# encoding:utf-8

import json

from django.shortcuts import render
from django.conf import settings
from django.http import *
from django.shortcuts import render
from django.shortcuts import render_to_response
from django.template import RequestContext
from django.conf import settings
from django.views.decorators.csrf import csrf_exempt

from eogserver.models import User,Source,Remind,Event,Dict



def generate_retu_info( code,msg ):
    return { 'code':code,'msg':msg }
def generate_success():
    return generate_retu_info( 1,'' )
def generate_failure( msg ):
    return generate_retu_info( 0,msg )


@csrf_exempt
def app_register(request):
    retu_obj = generate_success()
    return HttpResponse(json.dumps(retu_obj))

@csrf_exempt
def app_getregisterinfo(request):
    retu_obj = generate_success()
    return HttpResponse(json.dumps(retu_obj))

@csrf_exempt
def app_getstates(request):
    retu_obj = generate_success()
    return HttpResponse(json.dumps(retu_obj))

@csrf_exempt
def app_subscription(request):
    retu_obj = generate_success()
    return HttpResponse(json.dumps(retu_obj))

@csrf_exempt
def app_page_online(request):
    retu_obj = generate_success()
    return HttpResponse(json.dumps(retu_obj))







@csrf_exempt
def test(request):
    # this is only for test
    objs = [] 
    for item in Source.objects.all():
        objs.append( {
            'id':item.id,
            'textid':item.textid,
            'location':item.location,
            'mark':item.mark,
            'state':item.state,
        } )

    retu_obj = generate_failure( '中文')
    retu_obj['data'] = objs
    return HttpResponse(json.dumps(retu_obj))




