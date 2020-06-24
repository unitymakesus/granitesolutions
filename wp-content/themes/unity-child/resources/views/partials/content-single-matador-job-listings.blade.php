@php

$post_id = get_the_ID();
$general_location = get_post_meta($post_id, 'job_general_location', true);
$employment_type = get_post_meta($post_id, 'employmentType', true);
$category = get_the_term_list($post_id, 'matador-categories', '', ', ');
$bullhorn_job_id = get_post_meta($post_id, 'bullhorn_job_id', true);

@endphp



<article class="container-full" {!! post_class() !!}>
  <header class="page-header">
    <h1 class="entry-title">{!! get_the_title() !!}</h1>
    <div class="meta">
      @if (!empty($category) && !is_wp_error($category))
        <div>
          <span class="screen-reader-text">{{ __('Job Category:', 'sage') }}</span>
          <strong>{{ strip_tags($category) }}</strong>
        </div>
      @endif
      @if ($general_location)
        <em><span class="screen-reader-text">{{ __('Job Location:', 'sage') }}</span>{{ $general_location }}</em>
      @endif
    </div>
  </header>
  <div class="entry-content container">
    <div class="post-content">
      @php the_content() @endphp
    </div>
  </div>
</article>
