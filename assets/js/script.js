// Confirm delete
function confirmDelete(event, itemName) {
    if (!confirm(`Bạn có chắc chắn muốn xóa ${itemName} này không?`)) {
      event.preventDefault()
    }
  }
  
  // Add to cart
  function addToCart(courseId) {
    fetch(`actions/cart-actions.php?action=add&course_id=${courseId}`)
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          // Update cart count
          document.querySelector(".badge").textContent = data.count
          // Show success message
          alert("Đã chọn học phần!")
        } else {
          alert(data.message)
        }
      })
      .catch((error) => console.error("Error:", error))
  }
  
  // Remove from cart
  function removeFromCart(courseId) {
    fetch(`actions/cart-actions.php?action=remove&course_id=${courseId}`)
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          // Update cart count or remove badge if empty
          if (data.count > 0) {
            document.querySelector(".badge").textContent = data.count
          } else {
            document.querySelector(".badge").style.display = "none"
          }
          // Reload page to update cart items
          location.reload()
        } else {
          alert(data.message)
        }
      })
      .catch((error) => console.error("Error:", error))
  }
  
  // Preview image before upload
  function previewImage(input) {
    if (input.files && input.files[0]) {
      var reader = new FileReader()
  
      reader.onload = (e) => {
        document.getElementById("imagePreview").src = e.target.result
        document.getElementById("imagePreview").style.display = "block"
      }
  
      reader.readAsDataURL(input.files[0])
    }
  }
  
  // Initialize tooltips
  document.addEventListener("DOMContentLoaded", () => {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map((tooltipTriggerEl) => new bootstrap.Tooltip(tooltipTriggerEl))
  })
  
  