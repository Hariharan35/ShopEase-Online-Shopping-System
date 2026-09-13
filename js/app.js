/* =========================================================
   ShopEase - app.js
   Product class, AJAX product loading, jQuery UI behaviours
========================================================= */

/**
 * JavaScript Product class
 * Used client-side to model a product once it is fetched/rendered,
 * e.g. for building the cart summary from data attributes.
 */
class Product {
  constructor(id, name, price, image, quantity = 1) {
    this.id = id;
    this.name = name;
    this.price = parseFloat(price);
    this.image = image;
    this.quantity = quantity;
  }

  getLineTotal() {
    return this.price * this.quantity;
  }

  increaseQty(step = 1) {
    this.quantity += step;
    return this.quantity;
  }

  decreaseQty(step = 1) {
    this.quantity = Math.max(1, this.quantity - step);
    return this.quantity;
  }

  static fromCartRow(row) {
    return new Product(row.product_id, row.name, row.unit_price, row.image, row.quantity);
  }
}

/* ---------------------------------------------------------
   Toast helper (Bootstrap toast, triggered via jQuery)
--------------------------------------------------------- */
function showToast(message, type = 'success') {
  const $toast = $('#appToast');
  $toast.removeClass('text-bg-success text-bg-danger text-bg-info')
        .addClass('text-bg-' + type);
  $('#appToastBody').text(message);
  const toast = new bootstrap.Toast(document.getElementById('appToast'), { delay: 2200 });
  toast.show();
}

/* ---------------------------------------------------------
   Bump the cart badge with a small jQuery animate() effect
--------------------------------------------------------- */
function bumpCartBadge() {
  $('#cartCount').addClass('bump');
  setTimeout(() => $('#cartCount').removeClass('bump'), 350);
}

$(document).ready(function () {

  /* -------------------------------------------------------
     Fade the whole page content in on load
  ------------------------------------------------------- */
  $('main').hide().fadeIn(400);

  /* -------------------------------------------------------
     AJAX product loading (products.php) - no page reload
  ------------------------------------------------------- */
  function loadProducts() {
    if ($('#productsGrid').length === 0) return;

    const category = $('.cat-filter-link.active').data('category') || '';
    const search = $('#liveSearchInput').val() || '';
    const sort = $('#sortSelect').val() || 'default';

    $('#productsLoader').show();
    $('#productsGrid').slideUp(150);

    $.ajax({
      url: 'ajax/products.php',
      method: 'GET',
      data: { category: category, search: search, sort: sort },
      dataType: 'json',
      success: function (res) {
        $('#productsGrid').html(res.html);
        $('#resultCount').text(res.count + ' product' + (res.count === 1 ? '' : 's') + ' found');
        $('#productsLoader').hide();
        $('#productsGrid').slideDown(250);
      },
      error: function () {
        $('#productsLoader').hide();
        $('#productsGrid').html('<div class="col-12 text-center py-5 text-danger">Failed to load products. Please try again.</div>').slideDown(250);
      }
    });
  }

  if ($('#productsGrid').length) {
    // Set initial active category coming from PHP (index.php link / query string)
    if (typeof initialCategory !== 'undefined' && initialCategory) {
      $('.cat-filter-link').removeClass('active');
      $('.cat-filter-link[data-category="' + initialCategory + '"]').addClass('active');
    }
    if (typeof initialSearch !== 'undefined' && initialSearch) {
      $('#liveSearchInput').val(initialSearch);
    }
    loadProducts();
  }

  // Category filter click (jQuery selector + event)
  $(document).on('click', '.cat-filter-link', function (e) {
    e.preventDefault();
    $('.cat-filter-link').removeClass('active');
    $(this).addClass('active');
    loadProducts();
  });

  // Sort change
  $('#sortSelect').on('change', loadProducts);

  // Live search with debounce
  let searchTimer;
  $('#liveSearchInput').on('keyup', function () {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(loadProducts, 400);
  });

  /* -------------------------------------------------------
     Navbar search bar - subtle focus animation
  ------------------------------------------------------- */
  $('#navSearchInput').on('focus', function () {
    $(this).animate({ paddingLeft: '24px' }, 200);
  }).on('blur', function () {
    $(this).animate({ paddingLeft: '18px' }, 200);
  });

  /* -------------------------------------------------------
     Add to cart (grid / listing cards) - AJAX, no reload
  ------------------------------------------------------- */
  $(document).on('click', '.add-to-cart-btn', function () {
    const $btn = $(this);
    const id = $btn.data('id');
    const name = $btn.data('name');

    $btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i>');

    $.ajax({
      url: 'ajax/cart.php',
      method: 'POST',
      data: { action: 'add', product_id: id, quantity: 1 },
      dataType: 'json',
      success: function (res) {
        if (res.success) {
          $('#cartCount').text(res.cart.total_qty);
          bumpCartBadge();
          showToast(name + ' added to cart!');
        } else {
          showToast(res.message || 'Could not add to cart', 'danger');
        }
      },
      complete: function () {
        $btn.prop('disabled', false).html('<i class="fa-solid fa-cart-plus me-1"></i> Add to Cart');
      }
    });
  });

  /* -------------------------------------------------------
     Product details page: quantity stepper + add to cart
  ------------------------------------------------------- */
  $('#pdQtyPlus').on('click', function () {
    const $q = $('#pdQty');
    $q.val(parseInt($q.val()) + 1);
  });
  $('#pdQtyMinus').on('click', function () {
    const $q = $('#pdQty');
    $q.val(Math.max(1, parseInt($q.val()) - 1));
  });
  $('#pdAddToCart').on('click', function () {
    const $btn = $(this);
    const id = $btn.data('id');
    const name = $btn.data('name');
    const qty = parseInt($('#pdQty').val()) || 1;

    $.ajax({
      url: 'ajax/cart.php',
      method: 'POST',
      data: { action: 'add', product_id: id, quantity: qty },
      dataType: 'json',
      success: function (res) {
        if (res.success) {
          $('#cartCount').text(res.cart.total_qty);
          bumpCartBadge();
          showToast(qty + ' × ' + name + ' added to cart!');
        }
      }
    });
  });

  /* -------------------------------------------------------
     Password confirmation live check (register.php)
  ------------------------------------------------------- */
  $('#regConfirmPassword, #regPassword').on('keyup', function () {
    const pwd = $('#regPassword').val();
    const confirm = $('#regConfirmPassword').val();
    const $msg = $('#pwdMatchMsg');
    if (confirm.length === 0) {
      $msg.text('');
    } else if (pwd === confirm) {
      $msg.text('Passwords match ✓').css('color', 'green');
    } else {
      $msg.text('Passwords do not match ✗').css('color', 'red');
    }
  });

});
