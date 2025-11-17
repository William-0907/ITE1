from django.shortcuts import render

# Create your views here.
def index(request):
    return render(request, 'django_main/index.html', {'title' : 'Home', 'name' : 'index'})

def loginRegister(request):
    return render(request, 'django_main/login_register.html', {'title' : 'Login', 'name' : 'login'})

def dashboard(request):
    return render(request, 'django_main/dashboard.html', {'title' : 'Dashboard', 'name' : 'userDashboard'})