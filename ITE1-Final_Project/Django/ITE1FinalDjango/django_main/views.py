from django.shortcuts import render
from .forms import UserRegisterForm

# Create your views here.
def index(request):
    return render(request, 'django_main/index.html', {'title' : 'Home', 'name' : 'index'})

def loginRegister(request):
    form = UserRegisterForm
    return render(request, 'django_main/login_register.html', {'title' : 'Login', 'name' : 'login', form : 'form'})

def dashboard(request):
    return render(request, 'django_main/dashboard.html', {'title' : 'Dashboard', 'name' : 'userDashboard'})