<article class="container-full" {!! post_class() !!}>
  <header class="page-header">
    <h1 class="entry-title">{!! get_the_title() !!}</h1>
    <!-- @include('partials/entry-meta') -->
  </header>
  <div class="entry-content container">
     <!-- @if (has_post_thumbnail())
      @php
        $thumbnail_id = get_post_thumbnail_id( get_the_ID() );
        $alt = get_post_meta($thumbnail_id, '_wp_attachment_image_alt', true);
        $thumb_caption = get_the_post_thumbnail_caption(get_the_ID());
      @endphp
      <figure class="post-thumbnail">
        {!! get_the_post_thumbnail( get_the_ID(), 'large', ['alt' => $alt] ) !!}
        @if (!empty($thumb_caption))
          <figcaption class="thumb-caption">{!! $thumb_caption !!}</figcaption>
        @endif
      </figure>
    @endif -->

    <div class="post-content">
      @php the_content() @endphp
    </div>
  </div>
  <footer>
    {!! wp_link_pages(['echo' => 0, 'before' => '<nav class="page-nav"><p>' . __('Pages:', 'sage'), 'after' => '</p></nav>']) !!}
  </footer>
</article>
