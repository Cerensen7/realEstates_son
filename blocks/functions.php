<?php

function telefonFormat($telefon)
{
    return preg_replace('/^(5\d{2})(\d{3})(\d{4})$/', '($1) $2-$3', preg_replace('/\D/', '', $telefon));
}