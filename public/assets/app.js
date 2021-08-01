(function () {
  function getFlashMessage(isSuccess, messages) {
    return `<div class="w-full my-5 relative px-4 py-3 leading-normal text-${ isSuccess ? 'green' : 'red' }-700 bg-${ isSuccess ? 'green' : 'red' }-100 rounded-lg" role="alert">
        <span class="absolute inset-y-0 left-0 flex items-center ml-4">
          ${isSuccess ? `<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>` : `<svg class="w-6 h-6 fill-current" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" fill-rule="evenodd"></path></svg>`}
        </span>
        <p class="ml-6">${messages.map(item => item).join('<br/>')}</p>
        <span class="absolute inset-y-0 right-0 flex items-center mr-4" onclick="this.parentElement.remove()">
            <svg class="w-4 h-4 fill-current" role="button" viewBox="0 0 20 20"><path d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" fill-rule="evenodd"></path></svg>
        </span>
    </div>`
  }

  function leaseItem() {
    fetch(this.dataset.url, {
      method: 'GET',
    }).then(async response => {
      const data = await response.json();

      return response.ok ? Promise.resolve(data) : Promise.reject(data);
    })
      .then(async ({ data }) => {
        Swal.fire({
          title: 'Lease a book',
          html: data,
          showCloseButton: false,
          showCancelButton: false,
          showConfirmButton: false,
          didOpen: function (modal) {
            modal.style.padding = '0';
            modal.querySelector('#swal2-html-container').style.margin = '0';

            const form = document.forms['lease_form'];
            form.addEventListener('submit', function (e) {
                e.preventDefault();

                const formData = new FormData(form);

                fetch(form.action, {
                  body: formData,
                  method: 'POST',
                })
                  .then(async response => {
                    const body = {
                      status: response.status,
                      data: await response.json(),
                    };

                    return response.ok ? Promise.resolve(body) : Promise.reject(body);
                  })
                  .then(response => {
                    Swal.fire('The book is leased!', '', 'success');
                  }).catch(({ data: { errors, children } }) => {
                    const flashMessageContainer = form.querySelector('div');

                    if (errors && Array.isArray(errors) && errors.length) {
                      const message = getFlashMessage(false, errors);

                      flashMessageContainer.insertAdjacentHTML('afterbegin',message);

                      return;
                    }

                    if (children) {
                      for (let key in children) {
                        const item = children[key];

                        if (item.errors) {
                          const errors = item.errors.map(item => `<li>* ${item}</li>`).join('');

                          const list = form.querySelector('#errors-list');

                          if (list) {
                            list.remove();
                          }

                          form.querySelector(`[name*="${key}"]`).parentElement
                            .insertAdjacentHTML('beforeend',`<ul id="errors-list" class="mt-1 text-red-500 text-sm">${errors}</ul>`);
                        }
                      }
                    }
                  });
            });
          }
        });
      }).catch(({message}) => Swal.fire('', message, 'warning'))
  }

  document.querySelectorAll('.lease-button').forEach((element) => {
    element.onclick = leaseItem;
  });

  document.querySelectorAll('.list-item-delete').forEach(function (element) {
      element.onclick = function (e) {
        e.preventDefault();

        const url = this.href;

        Swal.fire({
          title: 'Are you sure?',
          text: "You won't be able to revert this!",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
          if (result.isConfirmed) {
            window.location = url;
          }
        })
      }
  });
})();
