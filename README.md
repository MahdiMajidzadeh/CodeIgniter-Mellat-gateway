# CodeIgniter-Mellat-gateway
[![StyleCI](https://styleci.io/repos/107120117/shield?branch=master)](https://styleci.io/repos/107120117)

## This library not maintained eny more

Codeigniter library for Iranian bank, Mellat.

## how to install

Copy `Mellat.php` and `nusoap` directory to `application/libraries` of your own project.

## how to use

First, load library:
```
$this->load->library('mellat');
```
second, set options:
```
$this->mellat->set_options(
    $terminal = '',
    $username = '',
    $password = '',
    $amount = 0,
    $order = 0,
    $callback = ''
    );
```

For send data to bank:
```
$refid = $this->mellat->call_bank();
```

send user to bank:
```
$this->mellat->redirect_to_bank($refid);
```

verify user payment:
```
$this->mellat->verify_payment($order, $refid)
```


## Contributor
- Mahdi Majidzadeh ([github](https://github.com/MahdiMajidzadeh))
