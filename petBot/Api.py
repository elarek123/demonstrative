from typing import Dict
from config import API_URL
import httpx



def storeUser(data: Dict):
    response = httpx.post(API_URL + '/auth/telegram', data=data, headers={"Accept": "application/json"} )
    return response

def confirmUser(data: Dict):
    response = httpx.post(API_URL +'/auth/confirmation', data=data, headers={"Accept": "application/json"} )
    return response

def getGeos():
    response = httpx.get(API_URL +'/geos/', headers={"Accept": "application/json"} )
    return response

def getProducts(data: Dict, token):
    hders = {"Accept": "application/json"}
    if token:
        hders.update({"Authorization": f"Bearer {token}"})
    response = httpx.get(API_URL + '/product-geos/', params=data, headers=hders)
    return response

def likeProduct(productId, token):
    hders = {"Accept": "application/json"}
    if token:
        hders.update({"Authorization": f"Bearer {token}"})
    url = API_URL + '/product-geos/' + productId + '/like'
    print(url)
    response = httpx.post(url, headers=hders)
    return response
def unLikeProduct(productId, token):
    hders = {"Accept": "application/json"}
    if token:
        hders.update({"Authorization": f"Bearer {token}"})
    url = API_URL + '/product-geos/' + productId + '/unlike'
    print(url)
    response = httpx.post(url, headers=hders)
    return response
def getLikedProducts(token):
    hders = {"Accept": "application/json"}
    if token:
        print(token)
        hders.update({"Authorization": f"Bearer {token}"})
    url = API_URL + '/product-geos/liked'
    response = httpx.get(url, headers=hders)
    return response