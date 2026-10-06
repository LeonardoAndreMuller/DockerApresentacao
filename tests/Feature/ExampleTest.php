<?php

test('the home page redirects to the products listing', function () {
    $response = $this->get('/');

    $response->assertRedirect('/produtos');
});
