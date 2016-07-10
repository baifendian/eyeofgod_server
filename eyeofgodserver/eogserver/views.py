# encoding:utf-8

import json,time

from django.utils import timezone
from django.shortcuts import render
from django.conf import settings
from django.http import *
from django.shortcuts import render
from django.shortcuts import render_to_response
from django.template import RequestContext
from django.conf import settings
from django.views.decorators.csrf import csrf_exempt

from eogserver.models import User,Source,Remind,Event,Dict




# 一个装饰器，将原函数返回的json封装成response对象
def return_http_json(func):
    def wrapper( *arg1,**arg2 ):
        d = func( *arg1,**arg2 )
        return HttpResponse( json.dumps(d) )
    return wrapper

def generate_retu_info( code,msg,**ext_info ):
    retu_data = { 'code':code,'msg':msg }
    for k in ext_info:
        retu_data[k] = ext_info[k]
    return retu_data

def generate_success(**ext_info):
    return generate_retu_info( 1,'',**ext_info )

def generate_failure( msg,**ext_info ):
    return generate_retu_info( 0,msg,**ext_info )

def generate_cur_time_stamp():
    return int(time.mktime(timezone.now().timetuple()))

# 判断POST请求中包含的参数是否都存在，若存在，则拿出来并返回
def check_keys(request_data,keys):
    retu_dict = generate_success( data = {} )
    for k in keys:
        d = request_data.get(k)
        if d:
            retu_dict['data'][k] = d
        else:
            return generate_failure( u'url请求缺少相关参数 : %s' % k )
    return retu_dict

# 获取Dict表的数据，放到一个python的字典对象中返回
def query_all_dict_table_items():
    data = {}
    for item in Dict.objects.all():
        data[item.textid] = item.text
    return data

def get_textid_userfriendlystr():
    return {
        1:'toliet',2:'toliet',3:'shower',4:'toliet',5:'toliet',6:'shower',
        7:'eggchair',8:'restroom',9:'restroom'
    }

def get_textid_sex():
    return {
        1:1,2:1,3:1,8:1,
        4:0,5:0,6:0,9:0,
    }







@csrf_exempt
@return_http_json
def sensor_postdata(request):
    keys = ['mark','state','timestamp']
    retu_dict = check_keys(request.POST,keys)
    if retu_dict['code'] != 1:
        retu_obj = generate_failure( retu_dict['msg'] )
    else:
        data = retu_dict['data']
        info_dict = {}
        for k in request.POST.viewkeys():
            info_dict[k] = request.POST.get(k)
        event = Event(mark = data['mark'],
                      state = data['state'],
                      timestamp = data['timestamp'],
                      info = str(info_dict))
        event.save() 
        retu_obj = generate_success()
    return retu_obj

@csrf_exempt
def app_get_page(request,pagename):
    allowed_page_names = [ 'overview','registered','toliet',
                           'shower','eggchair','restroom']
    if pagename not in allowed_page_names:
        return HttpResponse(json.dumps(generate_failure(u'找不到相关页面：%s' % pagename)))

    retu_dict = check_keys(request.POST,['mac'])
    # 测试代码
    #retu_dict = generate_success( data={'mac':'test2'} )
    if retu_dict['code'] == 0:
        return HttpResponse(json.dumps( generate_failure(retu_dict['msg']) ))
    if get_user_info_by_mac(retu_dict['data']['mac']) == None:
        u = 'eogserver/registered.html'
    else:
        u = 'eogserver/%s.html' % pagename
    return render_to_response(u,{},context_instance=RequestContext(request))

@csrf_exempt
@return_http_json
def app_get_state(request,pagename):
    allowed_page_names = {
                           'overview':app_get_overview,
                           'toliet':app_get_toliet,
                           'shower':app_get_shower,
                           'eggchair':app_get_eggchair,
                           'restroom':app_get_restroom
                         }
    if pagename not in allowed_page_names.keys():
        return generate_failure(u'找不到相关页面：%s' % pagename)

    retu_dict = check_keys(request.POST,['mac'])
    # 测试代码
    retu_dict = generate_success( data={'mac':'test1'} )
    if retu_dict['code'] == 0:
        return generate_failure(retu_dict['msg'])
    user_info = get_user_info_by_mac(retu_dict['data']['mac'])
    if user_info == None:
        return generate_failure(u'传入的mac无效')
    return allowed_page_names[pagename](user_info)   

def app_get_overview(user_info):
    textid_userfriendlystr = get_textid_userfriendlystr()
    retu_obj = {}
    for index in textid_userfriendlystr.keys():
        key = textid_userfriendlystr[int(index)]
        if retu_obj.get(key) == None:
            retu_obj[key] = { 'state':1,'detail':{} }
        for item in find_source_type_and_user_connection( user_info['id'],int(index) ):
            if item['state'] == 0:
                continue

            location = item['location']
            if retu_obj[key]['detail'].get(location) == None:
                retu_obj[key]['detail'][location] = 0
            retu_obj[key]['detail'][location] += 1
    for key in retu_obj:
        if len(retu_obj[key]['detail'].keys()) == 0:
            retu_obj[key]['state'] = 0
    return generate_success( data=retu_obj )

def app_get_toliet(user_info):
    return app_get_toliet_shower_eggchair(user_info,'toliet')

def app_get_shower(user_info):
    return app_get_toliet_shower_eggchair(user_info,'shower')

def app_get_restroom(user_info):
    return app_get_toliet_shower_eggchair(user_info,'restroom')

def app_get_toliet_shower_eggchair(user_info,text_type_str):
    retu_obj = { 
        'sex':user_info['sex'],
        1:{},                       # 男
        0:{}                        # 女
    }   
    for textid,value in get_textid_userfriendlystr().items():
        if value != text_type_str:
            continue
        for item in find_source_type_and_user_connection( user_info['id'],textid ):
            sex = get_textid_sex()[item['textid']]
            location = item['location']
            if retu_obj[sex].get(location) == None:
                retu_obj[sex][location] = []
            retu_obj[sex][location].append(item)
    return generate_success(data=retu_obj)

def app_get_eggchair(user_info):
    retu_obj = {}
    for textid,value in get_textid_userfriendlystr().items():
        if value != 'toliet':
            continue
        for item in find_source_type_and_user_connection( user_info['id'],textid ):
            location = item['location']
            if retu_obj.get(location) == None:
                retu_obj[location] = []
            retu_obj[location].append( item )
    return generate_success( data=retu_obj )


@csrf_exempt
def app_get_registeredinfo(request):
    retu_dict = check_keys(request.POST,['mac'])
    # 测试代码
    retu_dict = generate_success( data={'mac':'test1'} )
    if retu_dict['code'] == 0:
        return HttpResponse(json.dumps( generate_failure(retu_dict['msg']) ))
    user_info = get_user_info_by_mac(retu_dict['data']['mac'])
    if user_info == None:
        return HttpResponse(json.dumps( generate_failure(u'传入的mac无效') ))
    else:
        return HttpResponse(json.dumps( generate_success(data=user_info) ))

def get_user_info_by_mac(mac):
    querys = User.objects.filter(mac=mac)
    if len(querys) == 0:
        return None
    else:
        q = querys[0]
        data = {
            'id':q.id,
            'sex':q.sex,
            'location':q.location,
            'advanced':q.advanced,
            'mac':q.mac,
            'token':q.token,
            'createtime':q.createtime,
            'modifytime':q.modifytime
        }
        return data

# 获取某一个资源的当前状态
def find_source_type_and_user_connection(user_id,text_id):
    info_dict = query_all_dict_table_items()
    query = Source.objects.filter( textid = text_id )
    data = []
    for item in query:
        d = { 
            'sourceid':item.id,
            'textid':item.textid,
            'text':info_dict[item.textid],
            'location':item.location,
            'state':item.state
        }   
        if len(Remind.objects.filter( userid=user_id,sourceid=d['sourceid'],state=1 )) == 0:
            s = 0 
        else:
            s = 1 
        d['subscription'] = s 
        data.append(d)
    return data



















@csrf_exempt
@return_http_json
def app_register(request):
    keys = ['sex','location','mac','advanced','token']
    retu_dict = check_keys( request.POST,keys )
    if retu_dict['code'] != 1:
        retu_obj = generate_failure( retu_dict['msg'] )
    else:
        querys = User.objects.filter( mac = retu_dict['data']['mac'] )
        cur_time = generate_cur_time_stamp()

        if len(querys) == 0:
            u = User(
                sex = retu_dict['data']['sex'],
                location = retu_dict['data']['location'],
                advanced = retu_dict['data']['advanced'],
                mac = retu_dict['data']['mac'],
                token = retu_dict['data']['token'],
                createtime = cur_time,
                modifytime = cur_time
            )
        else:
            u = querys[0]
            u.sex = retu_dict['data']['sex']
            u.location = retu_dict['data']['location']
            u.advanced = retu_dict['data']['advanced']
            u.mac = retu_dict['data']['mac']
            u.token = retu_dict['data']['token']
            u.modifytime = cur_time
        u.save()
        retu_obj = generate_success()
    return retu_obj

@csrf_exempt
@return_http_json
def app_getregisterinfo(request):
    keys = ['mac']
    retu_dict = check_keys( request.POST,keys )
    if retu_dict['code'] != 1:
        return generate_failure( retu_dict['msg'] )

    user_info = get_user_info_by_mac(retu_dict['data']['mac'])
    if user_info:
        return generate_failure(data=user_info)
    else:
        return generate_failure(u'传入的mac无效')

@csrf_exempt
@return_http_json
def app_getstates(request):
    textid = request.POST.get('textid')
    if textid == None:
        querys = Source.objects.all()
    else:
        querys = Source.objects.filter(textid = textid)
    dict_table_data = query_all_dict_table_items()
    data = []
    for item in querys:
        data.append({
            'textid':item.textid,
            'text':dict_table_data[item.textid],
            'location':item.location,
            'state':item.state,
        })
    retu_obj = generate_success(data=data)
    return retu_obj

@csrf_exempt
@return_http_json
def app_subscription(request):
    keys = ['mac','sourceid','subscription']
    retu_dict = check_keys( request.POST,keys )
    if retu_dict['code'] != 1:
        return generate_failure( retu_dict['msg'] )
    user_info = get_user_info_by_mac(retu_dict['data']['mac'])
    if user_info == None:
        return generate_failure( u'传入的mac无效' )
    querys = Source.objects.filter( id=retu_dict['data']['sourceid'] )
    if len(querys) == 0:
        return generate_failure( u'传入的sourceid不存在' )
    r = Remind( userid = user_info['id'],
                sourceid = retu_dict['data']['sourceid'],
                state = retu_dict['data']['subscription'],
                createtime = generate_cur_time_stamp() )
    r.save()
    return generate_success()








def te(request):
    keys = ['mac','childurl']
    retu_dict = check_keys( request.POST,keys )
    if retu_dict['code'] != 1:
        return HttpResponse(json.dumps(generate_failure( retu_dict['msg'] )))

    #测试数据
    #retu_dict = generate_success( data={} )
    #retu_dict['data']['mac'] = 'test1'
    #retu_dict['data']['childurl'] = 'toliet'
    

    func_map = {
        'register':retu_page_register,
        'overview':retu_page_overview,
        'toliet':retu_page_toliet,
        'shower':retu_page_shower,
        'eggchair':retu_page_eggchair,
        'restroom':retu_page_restroom,
    }
    user_info = get_user_info_by_mac(retu_dict['data']['mac'])
    func = func_map[retu_dict['data']['childurl']]
    page,obj = func(user_info)
    return render_to_response(page,obj,context_instance=RequestContext(request))


def retu_page_register(user_info):
    d = [user_info] if user_info else []
    return ('eogserver/register.html',{'data':d})

def check_user_registered(func_obj):
    def wrapper(user_info):
        if user_info == None:
            return retu_page_register(None)
        else:
            return func_obj(user_info)
    return wrapper



@check_user_registered
def retu_page_overview(user_info):
    data = []
    for index in query_all_dict_table_items().keys():
        data += find_source_type_and_user_connection(user_info['id'],int(index))
    return ('eogserver/overview.html',{'data':data})

@check_user_registered
def retu_page_toliet(user_info):
    if user_info['sex'] == 1:
        data = find_source_type_and_user_connection(user_info['id'],1) + \
                find_source_type_and_user_connection(user_info['id'],2)
    else:
        data = find_source_type_and_user_connection(user_info['id'],4) + \
                find_source_type_and_user_connection(user_info['id'],5)
    return ('eogserver/toliet.html',{'data':data})


@check_user_registered
def retu_page_shower(user_info):
    if user_info['sex'] == 1:
        data = find_source_type_and_user_connection(user_info['id'],3)
    else:
        data = find_source_type_and_user_connection(user_info['id'],6)
    return ('eogserver/shower.html',{'data':data})


@check_user_registered
def retu_page_eggchair(user_info):
    data = find_source_type_and_user_connection(user_info['id'],7)
    return ('eogserver/eggchair.html',{'data':data})

@check_user_registered
def retu_page_restroom(user_info):
    if user_info['sex'] == 1:
        data = find_source_type_and_user_connection(user_info['id'],8)
    else:
        data = find_source_type_and_user_connection(user_info['id'],9)
    return ('eogserver/restroom.html',{'data':data})






@csrf_exempt
def test2(request):
    return render_to_response('eogserver/toliet.html',{},context_instance=RequestContext(request))


@csrf_exempt
@return_http_json
def test(request):
    retu_dict = generate_success( data={} )
    retu_dict['data']['mac'] = 'test1'
    retu_dict['data']['childurl'] = 'toliet'

    func_map = { 
        'register':retu_page_register,
        'overview':retu_page_overview,
        'toliet':retu_page_toliet,
        'shower':retu_page_shower,
        'eggchair':retu_page_eggchair,
        'restroom':retu_page_restroom,
    }   
    user_info = get_user_info_by_mac(retu_dict['data']['mac'])
    func = func_map[retu_dict['data']['childurl']]
    page,obj = func(user_info)
    return obj
    return render_to_response(page,obj,context_instance=RequestContext(request))





'''
@csrf_exempt
def test(request):
    # this is only for test
    print '\n\n\n',dict(request.POST)
    objs = [] 
    for item in Source.objects.all():
        objs.append( {
            'id':item.id,
            'textid':item.textid,
            'location':item.location,
            'mark':item.mark,
            'state':item.state,
        } )

    obj = {'data':[query_all_dict_table_items()]}
    return render_to_response('eogserver/overview.html',obj,context_instance=RequestContext(request))

    retu_obj = generate_failure( u'中文',data=objs,test='123')
    return HttpResponse(json.dumps(query_all_dict_table_items()))
'''



