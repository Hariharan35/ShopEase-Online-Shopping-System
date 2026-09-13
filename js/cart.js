/* =========================================================
   ShopEase - cart.js
   Handles cart.php: render items, qty +/-, remove, totals
========================================================= */

$(document).ready(function () {

  if ($('#cartItemsWrap').length === 0) return; // Only run on cart.php

  const DELIVERY_FEE = 49;
  const FREE_DELIVERY_THRESHOLD = 999;

  function renderCart(cart) {
    const $wrap = $('#cartItemsWrap');
    $wrap.empty();

    if (!cart.items || cart.items.length === 0) {
      $('#emptyCartMsg').fadeIn(300);
      $('#cartContainer .col-lg-4').hide();
      updateTotals(0);
      $('#cartCount').text(0);
      return;
    }

    $('#emptyCartMsg').hide();
    $('#cartContainer .col-lg-4').show();

    cart.items.forEach(function (row) {
      // Build a Product instance from cart row (JS Product class in app.js)
      const product = Product.fromCartRow(row);

      const $item = $(`
        <div class="cart-item" data-cart-id="${row.cart_id}">
          <img src="${row.image}" alt="${row.name}">
          <div class="flex-grow-1">
            <div class="item-name">${row.name}</div>
            <div class="text-muted small">₹${product.price.toFixed(2)} each</div>
            <div class="qty-selector mt-2">
              <button class="qty-btn cart-qty-minus">−</button>
              <input type="text" class="cart-qty-input" value="${row.quantity}" readonly>
              <button class="qty-btn cart-qty-plus">+</button>
            </div>
          </div>
          <div class="text-end">
            <div class="fw-bold mb-2 line-total">₹${product.getLineTotal().toFixed(2)}</div>
            <button class="remove-btn" title="Remove item"><i class="fa-solid fa-trash"></i></button>
          </div>
        </div>
      `);

      $item.hide();
      $wrap.append($item);
      $item.fadeIn(300).slideDown(250); // jQuery fade + slide combo
    });

    updateTotals(cart.total_amount);
    $('#cartCount').text(cart.total_qty);
  }

  function updateTotals(subtotal) {
    const delivery = subtotal === 0 ? 0 : (subtotal >= FREE_DELIVERY_THRESHOLD ? 0 : DELIVERY_FEE);
    const total = subtotal + delivery;
    $('#summarySubtotal').text('₹' + subtotal.toFixed(2));
    $('#summaryDelivery').text(delivery === 0 ? 'FREE' : '₹' + delivery.toFixed(2));
    $('#summaryTotal').text('₹' + total.toFixed(2));
  }

  function fetchCart() {
    $.ajax({
      url: 'ajax/cart.php',
      method: 'POST',
      data: { action: 'get' },
      dataType: 'json',
      success: function (res) {
        if (res.success) renderCart(res.cart);
      }
    });
  }

  // Initial load
  fetchCart();

  // Increase quantity
  $(document).on('click', '.cart-qty-plus', function () {
    const $item = $(this).closest('.cart-item');
    const cartId = $item.data('cart-id');
    const $input = $item.find('.cart-qty-input');
    const newQty = parseInt($input.val()) + 1;
    updateQuantity(cartId, newQty);
  });

  // Decrease quantity
  $(document).on('click', '.cart-qty-minus', function () {
    const $item = $(this).closest('.cart-item');
    const cartId = $item.data('cart-id');
    const $input = $item.find('.cart-qty-input');
    const newQty = parseInt($input.val()) - 1;
    updateQuantity(cartId, newQty); // if 0, ajax/cart.php removes the row
  });

  function updateQuantity(cartId, qty) {
    $.ajax({
      url: 'ajax/cart.php',
      method: 'POST',
      data: { action: 'update', cart_id: cartId, quantity: qty },
      dataType: 'json',
      success: function (res) {
        if (res.success) renderCart(res.cart);
      }
    });
  }

  // Remove item with a slide-up animation before refresh
  $(document).on('click', '.remove-btn', function () {
    const $item = $(this).closest('.cart-item');
    const cartId = $item.data('cart-id');

    $item.slideUp(300, function () {
      $.ajax({
        url: 'ajax/cart.php',
        method: 'POST',
        data: { action: 'remove', cart_id: cartId },
        dataType: 'json',
        success: function (res) {
          if (res.success) {
            renderCart(res.cart);
            showToast('Item removed from cart', 'info');
          }
        }
      });
    });
  });

});
