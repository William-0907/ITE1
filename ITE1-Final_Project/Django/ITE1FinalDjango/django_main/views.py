from django.shortcuts import render

# Create your views here.
def index(request):
    return render(request, 'django_main/index.html', {'title' : 'Home', 'name' : 'index'})